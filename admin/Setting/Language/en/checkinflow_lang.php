<?php
// Language file for settings page

// Heading
$lang['heading_title']                    = 'Checkin Flow';

// Text
$lang['text_edit']                        = 'Edit Setting';
$lang['text_image']                       = 'Select Image';
$lang['text_clear']                       = 'Clear';
$lang['text_success']                     = 'Success: You have modified settings!';

// Entries for general settings
$lang['entry_general'] = [
    'site_title'                      => 'Site Title',
    'site_tagline'                    => 'Site Tagline',
    'site_logo'                    	=> 'Site Logo',
    'site_icon'                       => 'Site Icon',
    'meta_title'                 => 'Meta Title',
    'meta_description'           => 'Meta Tag Description',
    'meta_keyword'               => 'Meta Tag Keywords',
    'site_owner'                 => 'Site Owner',
    'address'                 	=> 'Address',
    'country'                    => 'Country',
    'state'                      => 'Region / State',
    'email'                      => 'E-Mail',
    'telephone'                  => 'Telephone',
    'fax'                        => 'Fax',
];

// Entries for library and issue settings
$lang['entry_library'] = [
    'library_fine'               => 'Fine',
    'issue_limit_books'          => 'Issue Limit - Books ',
    'issue_limit_days'           => 'Issue Limit - Days ',
    'auto_fine'                  => 'Automatic Fine',
    'receipt_prefix'             => 'Receipt Prefix',
    'display_stock'              => 'Book Stock Display',
    'stock_warning'              => 'Book Stock Warning',
    'mail_alert'              	=> 'Mail Alert',
    'sms_alert'              		=> 'SMS Alert',
    'delay_members'             	=> 'Delay members Warning',
];

// Entries for design settings
$lang['entry_design'] = [
    'site_homepage'              => 'Site Homepage',
    'front_theme'                => 'Front Theme',
    'front_template'             => 'Front Default Layout',
    'header_layout'              => 'Header Layout',
    'header_image'               => 'Header Image',
    'header_banner'              => 'Header Banner',
    'header_slider'              => 'Header Slider',
    'background_image'           => 'Background Image',
    'background_position'        => 'Background Position',
    'background_repeat'          => 'Background Repeat',
    'background_attachment'      => 'Background Attachment',
    'background_color'      	  => 'Background Color',
    'text_color'      			  => 'Text Color',
];

// Entries for FTP and mail settings
$lang['entry_ftp'] = [
    'ftp_host'      			  => 'FTP Host',
    'ftp_port'      			  => 'FTP Port',
    'ftp_username'      		  => 'FTP Username',
    'ftp_password'      		  => 'FTP Password',
    'ftp_root'      			  => 'FTP Root',
    'ftp_enable'      			  => 'FTP Enable',
];

$lang['entry_mail'] = [
    'mail_protocol'      		  => 'Mail Protocol',
    'mail_parameter'      		  => 'Mail Parameters',
    'smtp_host'      		      => 'SMTP Hostname',
    'smtp_username'      		  => 'SMTP Username',
    'smtp_password'      		  => 'SMTP Password',
    'smtp_port'      			  => 'SMTP Port',
    'smtp_timeout'      		  => 'SMTP Timeout',
];

// Entries for miscellaneous settings
$lang['entry_misc'] = [
    'ssl'                        => 'Use SSL',
    'robots'                     => 'Robots',
    'time_zone'                  => 'Time Zone',
    'date_format'                => 'Date Format',
    'time_format'                => 'Time Format',
    'pagination_limit_front'     => 'Pagination Limit (Front)',
    'pagination_limit_admin'     => 'Pagination Limit (Admin)',
    'seo_url'                    => 'Use SEO URLs',
    'file_max_size'	          => 'Max File Size',
    'file_extensions'            => 'Allowed File Extensions',
    'file_mimetypes'             => 'Allowed File Mime Types',
    'maintenance_mode'           => 'Maintenance Mode',
    'encryption_key'             => 'Encryption Key',
    'compression_level'          => 'Output Compression Level',
    'display_error'              => 'Display Errors',
    'log_error'                  => 'Log Errors',
    'error_log_filename'         => 'Error Log Filename',
    'status'                     => 'Status',
];

// Helps for various settings
$lang['help'] = [
    'ssl'                         => 'To use SSL, check with your host if a SSL certificate is installed and add the SSL URL to the catalog and admin config files.',
    'robots'                      => 'A list of web crawler user agents that shared sessions will not be used with. Use separate lines for each user agent.',
    'seo_url'                     => 'To use SEO URLs, apache module mod-rewrite must be installed and you need to rename the htaccess.txt to .htaccess.',
    'file_max_size'		          => 'The maximum image file size you can upload in
