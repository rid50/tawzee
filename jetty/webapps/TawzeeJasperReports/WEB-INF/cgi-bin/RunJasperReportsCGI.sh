#!/bin/bash

#export DISPLAY="DISPLAYVAR ":0.0
#export DISPLAY=:0


#JAVA_HOME="/var/www/java-se-7-ri"; export JAVA_HOME
#PATH=$PATH:"/var/www/java-se-7-ri/bin"
#PATH=$PATH:"/var/www/jamvm-1.5.4/bin";
PATH=$PATH:"/var/www/jamvm-2.0.0/bin";

#echo Content-type: text/plain
#echo ""
#echo $JAVA_HOME
#printenv


#jamvm -classpath /var/www/html/jetty/webapps/TawzeeJasperReports/WEB-INF/classes GraphicsEnvironmentTest

java -cp /var/www/html/jetty/webapps/TawzeeJasperReports/WEB-INF/classes:/var/www/html/jetty/webapps/TawzeeJasperReports/WEB-INF/lib/* \
 -Dcgi.server_name="$SERVER_NAME" \
 -Dcgi.query_string="$QUERY_STRING" \
 JasperReportsCGI
