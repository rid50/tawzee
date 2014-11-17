#!/bin/bash

JAVA_HOME="/var/www/java-se-7-ri"; export JAVA_HOME
PATH=$PATH:"/var/www/java-se-7-ri/bin"

#echo Content-type: text/plain
#echo ""
#echo $JAVA_HOME
#printenv

java -cp /var/www/html/jetty/webapps/TawzeeJasperReports/WEB-INF/lib/*:/var/www/html/jetty/webapps/TawzeeJasperReports/WEB-INF/classes \
 -Dcgi.server_name="$SERVER_NAME" \
 -Dcgi.query_string="$QUERY_STRING" \
 JasperReportsCGI
