#!/usr/bin/sh
#java -cp /var/www/html/jetty/webapps/TawzeeJasperReports/WEB-INF/lib/*:/var/www/html/jetty/webapps/TawzeeJasperReports/WEB-INF/classes \
# -Dcgi.server_name="$SERVER_NAME" \
# -Dcgi.query_string="$QUERY_STRING" \
# JasperReportsCGI

JAVA_HOME="/var/www/java-se-7-ri"; export JAVA_HOME
PATH=$PATH:"/var/www/java-se-7-ri/bin"
java -cp /var/www/html/jetty/webapps/TawzeeJasperReports/WEB-INF/lib/*:/var/www/html/jetty/webapps/TawzeeJasperReports/WEB-INF/classes -Dcgi.server_name="$SERVER_NAME" -Dcgi.query_string="$QUERY_STRING" JasperReportsCGI
 
#!/usr/local/bin/bash

# JAVA_HOME="/home/yarussor/java-se-7-ri"; export JAVA_HOME
# PATH=$PATH:"/home/yarussor/java-se-7-ri/bin"

# echo JAVA_HOME
# printenv

# java -cp /home/yarussor/public_html/tawzee/jetty/webapps/TawzeeJasperReports/WEB-INF/lib/*:/home/yarussor/public_html/tawzee/jetty/webapps/TawzeeJasperReports/WEB-INF/classes -Dcgi.server_name="$SERVER_NAME" -Dcgi.query_string="$QUERY_STRING" JasperReportsCGI
