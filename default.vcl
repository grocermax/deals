# This is a basic VCL configuration file for varnish.  See the vcl(7)
# man page for details on VCL syntax and semantics.
# 
# Default backend definition.  Set this to point to your content
# server.
# 

backend default {
    .host = "127.0.0.1";
    .port = "8080";
}

# Incoming request
# can return pass or lookup (or pipe, but not used often)
sub vcl_recv {
  # set default backend
  set req.backend = default;

  # to generate password =>  $ php -r 'echo base64_encode("foo:bar");'
  #if (req.request != "PURGE" && !req.url ~ "/api/og/" && !req.url ~ "/health" && !req.http.Authorization ~ "Basic ZGVtbzp0ZXN0NTY3OCE="){
   # error 401 "Restricted";
  #}


  #if (req.request != "PURGE" && (req.http.host ~ "^(?i)demo.hungrybuzz.info")
  #       && req.http.X-Forwarded-Proto !~ "(?i)http") {
   #   set req.http.x-Redir-Url = "http://demo.hungrybuzz.info:8080" + req.url;
    #  error 750 req.http.x-Redir-Url;
    #}

  # Accept-Encoding
  if (req.http.Accept-Encoding) {
    if (req.url ~ "\.(jpg|png|gif|gz|tgz|bz2|tbz|mp3|ogg)$") {
      # No point in compressing these
      remove req.http.Accept-Encoding;
    } elsif (req.http.Accept-Encoding ~ "gzip") {
      set req.http.Accept-Encoding = "gzip";
    } elsif (req.http.Accept-Encoding ~ "deflate" && req.http.user-agent !~ "MSIE") {
      set req.http.Accept-Encoding = "deflate";
    } else {
      # unkown algorithm
      remove req.http.Accept-Encoding;
    }
   }

  # lookup stylesheets & js in the cache
  if (req.url ~ "^/(build|images|img)") {
    return(lookup);
  }

  if (req.url ~ "^/api") {
    return(pass);
  }
 
  # for index
  if (req.url ~ "^/index\.html" || req.url ~ "^/index\.php" || req.url ~ "^/$") {
    return(lookup);
  }

  return(pass);
}

# called after recv and before fetch
# allows for special hashing before cache is accessed
sub vcl_hash {
  if (req.url ~ "^/build") {
    #set req.url = regsub(req.url, "\?v=\d+", "");
  }
  if (req.url ~ "^/index\.html" || req.url ~ "^/index\.php" || req.url ~ "^/$") {
    #hash_data(req.url + req.http.X-Version);
  }
 
}

# Before fetching from webserver
# returns pass or deliver
sub vcl_fetch {
  if (req.url ~ "^/(build|images|img)") {
    # removing cookie
    unset beresp.http.Set-Cookie;

    # Cache for 1 day
    set beresp.ttl = 1d;
    return(deliver);
  }

  if (req.url ~ "^/index\.html" || req.url ~ "^/index\.php" || req.url ~ "^/$") {
    unset beresp.http.Set-Cookie;

    # Cache for 5 mins
    set beresp.ttl = 300s;
    return(deliver);
  }
}

# called after fetch or lookup yields a hit
sub vcl_deliver {
  if (obj.hits > 0) {
    set resp.http.X-Varnish-Cache = "HIT";
  }
  else {
    set resp.http.X-Varnish-Cache = "MISS";
  }
  return (deliver);
}

sub vcl_error {
  if (obj.status == 401) {
    set obj.http.Content-Type = "text/html; charset=utf-8";
    set obj.http.WWW-Authenticate = "Basic realm=Secured";
    synthetic {" 
     <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
     "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
     <HTML>
     <HEAD>
      <TITLE>Error</TITLE>
      <META HTTP-EQUIV='Content-Type' CONTENT='text/html;'>
     </HEAD>
     <BODY><H1>401 Unauthorized (varnish)</H1></BODY>
     </HTML>
   "};
   return (deliver);
  }
 
  if (obj.status == 750) {
    set obj.http.Location = obj.response;
    set obj.status = 302;
    return (deliver);
  }
}

# for purging
acl purge {
   # allow PURGE from localhost
  "localhost";
}

sub vcl_recv {
  # allow PURGE from localhost
  if (req.request == "PURGE") {
    if (!client.ip ~ purge) {
      error 405 "Not allowed.";
    }
    return (lookup);
   }
}

sub vcl_hit {
  if (req.request == "PURGE") {
     purge;
     error 200 "Purged.";
  }
  
  if (obj.ttl < 1s) {
   return (pass);
  }
}

sub vcl_miss {
  if (req.request == "PURGE") {
     purge;
     error 200 "Purged.";
  }
}




# 
# Below is a commented-out copy of the default VCL logic.  If you
# redefine any of these subroutines, the built-in logic will be
# appended to your code.
# sub vcl_recv {
#     if (req.restarts == 0) {
# 	if (req.http.x-forwarded-for) {
# 	    set req.http.X-Forwarded-For =
# 		req.http.X-Forwarded-For + ", " + client.ip;
# 	} else {
# 	    set req.http.X-Forwarded-For = client.ip;
# 	}
#     }
#     if (req.request != "GET" &&
#       req.request != "HEAD" &&
#       req.request != "PUT" &&
#       req.request != "POST" &&
#       req.request != "TRACE" &&
#       req.request != "OPTIONS" &&
#       req.request != "DELETE") {
#         /* Non-RFC2616 or CONNECT which is weird. */
#         return (pipe);
#     }
#     if (req.request != "GET" && req.request != "HEAD") {
#         /* We only deal with GET and HEAD by default */
#         return (pass);
#     }
#     if (req.http.Authorization || req.http.Cookie) {
#         /* Not cacheable by default */
#         return (pass);
#     }
#     return (lookup);
# }
# 
# sub vcl_pipe {
#     # Note that only the first request to the backend will have
#     # X-Forwarded-For set.  If you use X-Forwarded-For and want to
#     # have it set for all requests, make sure to have:
#     # set bereq.http.connection = "close";
#     # here.  It is not set by default as it might break some broken web
#     # applications, like IIS with NTLM authentication.
#     return (pipe);
# }
# 
# sub vcl_pass {
#     return (pass);
# }
# 
# sub vcl_hash {
#     hash_data(req.url);
#     if (req.http.host) {
#         hash_data(req.http.host);
#     } else {
#         hash_data(server.ip);
#     }
#     return (hash);
# }
# 
# sub vcl_hit {
#     return (deliver);
# }
# 
# sub vcl_miss {
#     return (fetch);
# }
# 
# sub vcl_fetch {
#     if (beresp.ttl <= 0s ||
#         beresp.http.Set-Cookie ||
#         beresp.http.Vary == "*") {
# 		/*
# 		 * Mark as "Hit-For-Pass" for the next 2 minutes
# 		 */
# 		set beresp.ttl = 120 s;
# 		return (hit_for_pass);
#     }
#     return (deliver);
# }
# 
# sub vcl_deliver {
#     return (deliver);
# }
# 
# sub vcl_error {
#     set obj.http.Content-Type = "text/html; charset=utf-8";
#     set obj.http.Retry-After = "5";
#     synthetic {"
# <?xml version="1.0" encoding="utf-8"?>
# <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
#  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
# <html>
#   <head>
#     <title>"} + obj.status + " " + obj.response + {"</title>
#   </head>
#   <body>
#     <h1>Error "} + obj.status + " " + obj.response + {"</h1>
#     <p>"} + obj.response + {"</p>
#     <h3>Guru Meditation:</h3>
#     <p>XID: "} + req.xid + {"</p>
#     <hr>
#     <p>Varnish cache server</p>
#   </body>
# </html>
# "};
#     return (deliver);
# }
# 
# sub vcl_init {
# 	return (ok);
# }
# 
# sub vcl_fini {
# 	return (ok);
# }
