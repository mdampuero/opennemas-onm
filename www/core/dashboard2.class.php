<?php

class Configuration
{
  private $configFile = 'config.inc.php';

  private $items = array();

  function __construct() { $this->parse(); }

  function __get($id) { return $this->items[ $id ]; }
  function __set($id,$v) { $this->items[ $id ] = $v; }

  function get_items() { $vbles = $this->items;return $vbles; }

  function set_items($vbles) { $this->items = $vbles;

	foreach ($this->items as $key => $vb) 
	{
		if ( $key == "SITE_URL" ) $this->items['SITE_URL'] = '\'http://\'.SITE';
		if ( $key == "SITE_URL_SSL" ) $this->items['SITE_URL_SSL'] = '\'https://\'.SITE';
		if ( $key == "SITE_URL_ADMIN" ) $this->items['SITE_URL_ADMIN'] = 'SITE_URL.SITE_ADMIN_PATH';
		if ( $key == "SITE_URL_ADMIN_SSL" ) $this->items['SITE_URL_ADMIN_SSL'] = 'SITE_URL_SSL.SITE_ADMIN_PATH';

		if ( $key == "URL" ) $this->items['URL'] = 'SITE_URL_ADMIN';
		if ( $key == "URL_PUBLIC" ) $this->items['URL_PUBLIC'] = 'SITE_URL';
		if ( $key == "RELATIVE_PATH" ) $this->items['RELATIVE_PATH'] = 'SITE_ADMIN_PATH';
		if ( $key == "PATH_APP" ) $this->items['PATH_APP'] = 'SITE_PATH.SITE_ADMIN_PATH';

		if ( $key == "MEDIA_PATH" ) $this->items['MEDIA_PATH'] = 'SITE_PATH.MEDIA_PATH_RELA';
		if ( $key == "MEDIA_PATH_URL" ) $this->items['MEDIA_PATH_URL'] = 'SITE_URL.MEDIA_PATH_RELA';
		if ( $key == "MEDIA_IMG_PATH" ) $this->items['MEDIA_IMG_PATH'] = 'MEDIA_PATH.MEDIA_IMG_RELA_PATH';
		if ( $key == "MEDIA_IMG_PATH_URL" ) $this->items['MEDIA_IMG_PATH_URL'] = 'MEDIA_PATH_URL.MEDIA_IMG_RELA_PATH';

		if ( $key == "BD_DSN" ) $this->items['BD_DSN'] = 'BD_TYPE.\'://\'.BD_USER.\':\'.BD_PASS.\'@\'.BD_HOST.\'/\'.BD_INST';

		$this->items[$key] = preg_replace('/\/\//', '/', $this->items[$key]);
		$this->items[$key] = preg_replace('/:\//', '://', $this->items[$key]);
	}

  }

  function get_conf_file() { return $this->configFile; }

  function parse()
  {
    $fh = fopen( $this->configFile, 'r' );
    while( $l = fgets( $fh ) )
    {
      if ( preg_match( '/^#/', $l ) == false && preg_match( '/^define/', $l ) == true)
      {
	if ( preg_match( '/.*;.*\/\/.*/', $l ) == true )
	{
	  preg_match( '/^define\(\'(.*?)\', \'(.*?)\'\);.*/');
	  $this->items[ $found[1] ] = $found[2];
	}
	else {
	  
          if ( preg_match( '/^define \(\'(.*?)\', \'(.*?)\'\);$/', $l, $found ) == true ) 
	  {
              $this->items[ $found[1] ] = $found[2];
	  }
	  else
	  {
	      if ( preg_match( '/^define \(\'(.*?)\', (.*?)\);$/', $l, $found ) == true )
	      $this->items[ $found[1] ] = $found[1];
	  }
		
	}

      }
    }
    fclose( $fh );
    return;
  }

  function save()
  {
    //$vbles = $this->items;

    $nf = '';
    $fh = fopen( $this->configFile, 'r' );
    while( $l = fgets( $fh ) )
    {
      if ( preg_match( '/^#/', $l ) == false && preg_match( '/^define/', $l ) == true )
      {
	if (preg_match( '/^define \(\'(.*?)\', \'(.*?)\'\);$/', $l, $found ) == true )
	{ $nf .= "define ('".$found[1]."', '".$this->items[$found[1]]."');\n"; }
	else
	{ preg_match( '/^define \(\'(.*?)\', .*\);$/', $l, $found );
	$nf .= "define ('".$found[1]."', ".$this->items[$found[1]].");\n"; }
	//echo "define ('".$found[1]."', '".$this->items[$found[1]]."');<br>";
      }
      else {$nf .= $l;
	//echo $l."<br>";
	 }
    }
	
    fclose( $fh );
    copy( $this->configFile, $this->configFile.'.bak' );
    $fh = fopen( $this->configFile, 'w' );
    fwrite( $fh, $nf );
    fclose( $fh );
  }

}
?>