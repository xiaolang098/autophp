<?php



class plugin_end extends plugin_abstract{
    
    
    
    public function run($tag, plugin_context &$ptx){
        echo "\r\n <br /> hello, im plugin backend after your action run! \r\n <br /> ";
    }
    
    
}