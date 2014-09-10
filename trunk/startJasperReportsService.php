<?php 
	//$cmd = "jetty/java -jar start.jar";
	//$cmd = "/var/www/html/jetty/bin/jetty.sh start";
	//$cmd = "ls -lart";
	//shell_exec('ls -lart');
    //if (!(substr(php_uname(), 0, 7) == "Windows")) { 
        //exec($cmd . " > /dev/null &");   
        //shell_exec($cmd . " > /var/www/html/jetty/bin/jetty.log &");   
    //} 
    //else { 
    //    pclose(popen("start /B ". $cmd, "r"));  
    //} 
	
	
	exec('ls -lart 2>&1', $output);
	//exec('JETTY_HOME=/var/www/html/jetty');
	//exec('/var/www/html/jetty/bin/jetty.sh start 2>&1', $output);
	//exec('/home/yarussor/public_html/tawzee/jetty/bin/jetty.sh start 2>&1', $output);
	//exec('echo $JETTY_HOME 2>&1', $output);
	$str = print_r($output, false);
	throw new Exception($str);
?> 