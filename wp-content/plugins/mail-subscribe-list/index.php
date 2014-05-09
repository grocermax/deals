<div class="wrap">
	
	<?php screen_icon( 'themes' ); ?>
	<h2>Mailing List Subscribers</h2>
	
	<div id="poststuff">
	
		<div id="post-body" class="metabox-holder columns-2">
		
			<!-- main content -->
			<div id="post-body-content">
				
			<form method="post" action="?page=<?php echo $_GET['page']; ?>">
            <input name="sml_remove" value="1" type="hidden" />
            			<?php 
						if ($_SERVER['REQUEST_METHOD']=="POST" and $_POST['sml_remove']) {
							if ($_GET['rem']) $_POST['rem'][] = $_GET['rem'];
							$count = 0;
							if (is_array($_POST['rem'])) {
								foreach ($_POST['rem'] as $id) { 
									$wpdb->query("delete from ".$wpdb->prefix."sml where id = '".$id."' limit 1"); 
									$count++; 
								}
								$message = $count." subscribers have been removed successfully.";
							}
						}
						
						if ($_SERVER['REQUEST_METHOD']=="POST" and $_POST['sml_import']) {
							$correct = 0;
							if($_FILES['file']['tmp_name']) {
								if(!$_FILES['file']['error'])  {
									$file = file_get_contents ($_FILES['file']['tmp_name']);
									$lines = preg_split('/\r\n|\r|\n/', $file);
									if (count($lines)) {
										$sql = array();
										foreach ($lines as $data) {
											$data = explode(',', $data);
											$num = count($data);
											$row++;
											
											if (is_email(trim($data[0]))) {
												$c = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."sml where sml_email LIKE '".$wpdb->escape(trim($data[0]))."' limit 1", ARRAY_A);
												if (!is_array($c)) {											
													$wpdb->query("INSERT INTO ".$wpdb->prefix."sml (sml_email, sml_name) VALUES ('".$wpdb->escape(trim($data[0]))."', '".$wpdb->escape(trim($data[1]))."')");
													$correct++;
												} else { $exists++; }
											} else { $invalid++; }
										}
										
									} else { $message = 'Oh no! Your CSV file does not apear to be valid, please check the format and upload again.'; }
								
									if (!$message) {
										$message = $correct.' records have been imported. '.($invalid?$invalid.' could not be imported due to invalid email addresses. ':'').($exists?$exists.' already exists. ':'');
									}
								
								} else {
									$message = 'Ooops! There seems to of been a problem uploading your csv';
								}
							}								
						}
						//echo $sql;
						if ($message) { echo '<div style="padding: 5px;" class="updated"><p>'.$message.'</p></div>'; }
						
            			?>
					
						<table cellspacing="0" class="wp-list-table widefat fixed subscribers">
                          <thead>
                            <tr>
                                <th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
                                <th style="" class="manage-column column-name" id="name" scope="col">Name<span class="sorting-indicator"></span></th>
                                <th style="" class="manage-column column-email" id="email" scope="col"><span>Email Address</span><span class="sorting-indicator"></span></th>
                            </thead>
                        
                            <tfoot>
                            <tr>
                                <th style="" class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>
                                <th style="" class="manage-column column-name" scope="col"><span>Name</span><span class="sorting-indicator"></span></th>
                                <th style="" class="manage-column column-email" scope="col"><span>Email Address</span><span class="sorting-indicator"></span></th>
                            </tfoot>
                        
                            <tbody id="the-list">
                            
                            <?php 
                            
								$results = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."sml");
								if (count($results)<1) echo '<tr class="no-items"><td colspan="3" class="colspanchange">No mailing list subscribers have been added.</td></tr>';
								else {
									foreach($results as $row) {
	
										echo '<tr>
													<th class="check-column" style="padding:5px 0 2px 0"><input type="checkbox" name="rem[]" value="'.$row->id.'"></th>
													<td>'.$row->sml_name.'</td>
													<td>'.$row->sml_email.'</td>
											  </tr>';
											  
											  
											  
	
									}
								}
							
							?>
                            
                                
                            </tbody>
                        </table>
                        <br class="clear">
						<input class="button" name="submit" type="submit" value="Remove Selected" /> <a class="button" href="<?php echo plugins_url( 'export-csv.php', __FILE__ ); ?>">Export as CSV</a>
				</form>
				<br class="clear">
                
                
                <div class="meta-box-sortables">
                        <div class="postbox">
                        	
                       	  <h3><span>Import your own CSV File</span></h3>
                          <div class="inside">
                
                <p>This feature allows you to import your own csv (comma seperated values) file into &quot;Mail Subscribe List&quot;.</p>

                <form id="import-csv" method="post" enctype="multipart/form-data" action="?page=<?=$_GET['page'];?>">
                <input name="sml_import" value="1" type="hidden" />
                <p><label><input name="file" type="file" value="" /> CSV File</label></p>
                <p class="description">File must contain no header row, each record on its own line and only two comma seperated collumns in the order of email address followed by name. The name field is optional.</p>
                <p>Example: joe@blogs.com,Joe Blogs</p>
                
                
                
                <p class="submit"><input type="submit" value="Upload and Import CSV File" class="button-secondary" id="submit" name="submit"></p></form>
                </div></div></div>
                
                <br class="clear">
                
                <div class="meta-box-sortables">
                        <div class="postbox">
                        	
                       	  <h3><span>Mail Subscribe List - How to Use</span></h3>
                          <div class="inside">
                            	<p>This is a simple plugin that allows visitors to enter their name and email address on your website, the visitos details are then added to the subscribers list which is available to view and modify in the WordPress admin area. </p>
                            	<p>This plugin can be used not only for Mailing List subscriptions but can be used generally for collecting email address and/or peoples names that are visiting your website.</p>
                            	<p>The name/email form can not only be customised but can also be displayed on any WordPress page by using either the 'Subscribe Widget', WordPress shortcode [smlsubform] or from your WordPress theme by calling the php function.</p>
                           	<p>I developed this plugin as I could not find any plugin that simply allows users to submit their name and email address to a simple list viewable in the WordPress admin, all the plugins that I found had lots of extra features such as 3rd party integration, mass emailing and double opt-in, my clients do not need any of these features.</p>
                            	<p>Like this plugin? Please follow me on Twitter and Facebook</p>
                           	<p><strong>Extra Options</strong></p>
                       	    <p>I have developed some customisable options that allow you to change the way the plugin is displayed.</p>
                       	    <p>Below is an explanation of what each option does:-</p>
                            <ul>
                              <li><strong>&quot;prepend&quot;	</strong>-&gt;	Adds a paragraph of text just inside the top of the form.</li>
                              <li> <strong>&quot;showname&quot;	</strong>-&gt;	If true, this with show the name label and input field for capturing the users name.</li>
                              <li> <strong>&quot;nametxt&quot;	</strong>-&gt;	Text that is displayed to the left of the name input field.</li>
                              <li> <strong>&quot;nameholder&quot;	</strong>-&gt;	Text that is displayed inside the name input box as a place holder.</li>
                              <li> <strong>&quot;emailtxt&quot;	</strong>-&gt;	Text that is displayed to the left of the email input field.</li>
                              <li> <strong>&quot;emailholder&quot;	</strong>-&gt;	Text that is displayed inside the email input box as a place holder.</li>
                              <li> <strong>&quot;showsubmit&quot;</strong> -&gt; If true, this with show the submit button, return required to submit form.</li>
                              <li> <strong>&quot;submittxt&quot;</strong>	-&gt;	Text/value that will be displayed on the form submit button.</li>
                              <li> <strong>&quot;jsthanks&quot;</strong>	-&gt;	If true, this will display a JavaScript Alert Thank You message instead of a paragraph above the form.</li>
                              <li> <strong>&quot;thankyou&quot;	</strong>-&gt;	Thank you message that will be displayed when someone subscribes. (Will not show if blank)</li>
                            </ul>
<p><strong>Extra Options - How to Use (Short Code Method)</strong></p>
                       	    <p>Short codes can be used simply putting the code into your wordpress page, here is an example of the shortcode in use.</p>
                       	    <p><strong>[smlsubform prepend=&quot;&quot; showname=true nametxt=&quot;Name:&quot; nameholder=&quot;Name...&quot; emailtxt=&quot;Email:&quot; emailholder=&quot;Email Address...&quot; showsubmit=true submittxt=&quot;Submit&quot; jsthanks=false thankyou=&quot;Thank you for subscribing to our mailing list&quot;]</strong></p>
                       	    <p><strong>Extra Options - How to Use (PHP Method)</strong></p>
                       	    <p>The PHP method can be used by putting the following PHP code into your WordPress theme, here is an example of php code for your template.</p>
                       	    <p><strong>$args = array(<br />
                   	        'prepend' =&gt; '', <br />
                   	        'showname' =&gt; true,<br />
                   	        'nametxt' =&gt; 'Name:', <br />
                   	        'nameholder' =&gt; 'Name...', <br />
                   	        'emailtxt' =&gt; 'Email:',<br />
                   	        'emailholder' =&gt; 'Email Address...',
                   	        <br />
                   	        'showsubmit' =&gt; true,                   	        <br />
                   	        'submittxt' =&gt; 'Submit', <br />
                   	        'jsthanks' =&gt; false,<br />
                   	        'thankyou' =&gt; 'Thank you for subscribing to our mailing list'<br />
                   	        );<br />
                   	        echo smlsubform($args);</strong>                          </p>
                          </div>
                      </div> 
                    </div>
                
			</div> 
			
			
			<div id="postbox-container-1" class="postbox-container">
                    <div class="meta-box-sortables">
                        <div class="postbox">
                        	
                       	  <h3><span>Webforward</span></h3>
                            <div class="inside">
                            	
                                <p><a target="_blank" href="http://www.webfwd.co.uk/packages/wordpress-hosting/"><img style="margin-top:15px" src="<?php echo plugins_url( 'webfwdlogo.png', __FILE__ ); ?>"></a></p>

                                
                                
                                <p><em>We are an internet based service provider just a few miles north of Birmingham UK, we develop time-saving systems for businesses that are accessible from all devices. We have our own high speed resilient data centre which consist of web, database and streaming servers. We also provide IT support, consultancy and produce documents for businesses.</em></p>
                                <p><a class="button" href="http://www.webfwd.co.uk/packages/wordpress-hosting/" target="_blank">Visit our Website</a></p>
                                
                                <p>Please follow us on - <a class="button" target="_blank" href="https://twitter.com/webfwd">Twitter</a> <a class="button" target="_blank" href="https://www.facebook.com/pages/Webforward/208823852487018">Facebook</a></p>
                            </div>
                      </div> 
                    </div> 
                </div> 
            </div>
            <br class="clear">
	</div>
	
</div> 