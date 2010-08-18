<?php


class Apcstatus_IndexController extends Onm_Controller_Action
{

    public $infoAboutAPC = null;
    public $cache_user = null;
    public $mem =  null;
    public $cache = null;
    public $host = null;
    public $time =  null;
    
    public function preDispatch()
    {
        $this->cache_user = apc_cache_info('user', 1);  
        $this->mem = apc_sma_info();
        if(!$this->cache['num_hits']) { $this->cache['num_hits']=1; $this->time++; }  // Avoid division by 0 errors on a cache clear
        if(!function_exists('apc_cache_info')
           || !($this->cache=@apc_cache_info($cache_mode))) {
            echo "No cache info available.  APC does not appear to be running.";
        }
        $this->time = time();
        $this->host = getenv('HOSTNAME');
        if($this->host) { $this->host = '('.$this->host.')'; }
        
        parent::preDispatch();
    
    }
    
    /**
     * Route: apcstatus-index-index
     *  /svn/*
     */
    public function indexAction()
    {
        $command = $this->_getParam('command', null);
        
    }
    
    public $error_from_feed = null;
    public $apc_version = null;
    public $version_match = null;
    public $version_compare = null;
    public $new_version_feed = null;
    public $description_new_versions = null;
    
    /**
     * Route: apcstatus-index-versioncheck
     *  /apc-status/version-check
     */
    public function versioncheckAction()
    {
        $rss = @file_get_contents("http://pecl.php.net/feeds/pkg_apc.rss");
        
        
        if (!$rss){
            $this->error_from_feed = true;
        } else {
            $this->apc_version = phpversion('apc');
    
            preg_match('!<title>APC ([0-9.]+)</title>!', $rss, $this->version_match);
            $this->version_compare = version_compare($this->apc_version, $this->version_match[1], '>=');
            if ($this->version_compare) {
                $i = 3;
            } else {
                $i = -1;
            }
            
            preg_match_all('!<(title|description)>([^<]+)</\\1>!', $rss, $match);;
            next($match[2]);
            next($match[2]);
    
            while (list(,$v) = each($match[2])) {
                list(,$ver) = explode(' ', $v, 2);
                if ($i < 0 && version_compare($this->apc_version, $ver, '>=')) {
                    break;
                } else if (!$i--) {
                    break;
                }
                $this->description_new_versions = "<a href=\"http://pecl.php.net/package/APC/$ver\">".htmlspecialchars($v)."</a>";
                $this->description_new_versions .= nl2br(htmlspecialchars(current($match[2])))."</blockquote>";
                next($match[2]);
            }
        }
    }
    /**
     * Route: apcstatus-index-renderimages
     *  /apc-status/render-images
     */
    public function renderimagesAction()
    {
        
        $this->getHelper('viewRenderer')->setNoRender();
    }

    
    public $data = null;
    
    public function hoststatusAction()
    {
        $this->data['mem_size'] = $this->mem['num_seg']*$this->mem['seg_size'];
        $this->data['mem_avail']= $this->mem['avail_mem'];
        $this->data['mem_used'] = $this->data['mem_size']-$this->data['mem_avail'];
        $this->data['seg_size'] = $this->bsize($this->mem['seg_size']);
        $this->data['req_rate'] = sprintf("%.2f",
                                          ($this->cache['num_hits']+$this->cache['num_misses'])
                                          / ($this->time-$this->cache['start_time']));
        $this->data['hit_rate'] = sprintf("%.2f",
                                          ($this->cache['num_hits'])
                                          /($this->time-$this->cache['start_time']));
        $this->data['miss_rate'] = sprintf("%.2f",
                                           ($this->cache['num_misses'])
                                           /($this->time-$this->cache['start_time']));
        $this->data['insert_rate'] = sprintf("%.2f",
                                             ($this->cache['num_inserts'])
                                             /($this->time-$this->cache['start_time']));
        $this->data['req_rate_user'] = sprintf("%.2f",
                                               ($this->cache_user['num_hits']
                                                +$this->cache_user['num_misses'])
                                               /($this->time-$this->cache_user['start_time']));
        $this->data['hit_rate_user'] = sprintf("%.2f",
                                               ($this->cache_user['num_hits'])
                                               /($this->time-$this->cache_user['start_time']));
        $this->data['miss_rate_user'] = sprintf("%.2f",
                                                ($this->cache_user['num_misses'])
                                                /($this->time-$this->cache_user['start_time']));
        $this->data['insert_rate_user'] = sprintf("%.2f",
                                                  ($this->cache_user['num_inserts'])
                                                  /($this->time-$this->cache_user['start_time']));
        $this->data['apcversion'] = phpversion('apc');
        $this->data['phpversion'] = phpversion();
        $this->data['number_files'] = $this->cache['num_entries']; 
        $this->data['size_files'] = $this->bsize($this->cache['mem_size']);
        $this->data['number_vars'] = $this->cache_user['num_entries'];
        $this->data['size_vars'] = $this->bsize($this->cache_user['mem_size']);
        $this->data['ini_apc'] = ini_get_all('apc');
        $this->data['graphics_avail'] = $this->graphics_avail();
       
    }
    
    // pretty printer for byte values
    //
    private function bsize($s)
    {
        foreach (array('','K','M','G') as $i => $k) {
            if ($s < 1024) break;
            $s/=1024;
        }
        return sprintf("%5.1f %sBytes",$s,$k);
    }
    
    private function graphics_avail() {
        return extension_loaded('gd');
    }
    
    
}