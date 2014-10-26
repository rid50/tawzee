#!/bin/bash
java -cp /var/www/html/jetty/webapps/TawzeeJasperReports/WEB-INF/lib/*:/var/www/html/jetty/webapps/TawzeeJasperReports/WEB-INF/classes -Dcgi.server_name="$SERVER_NAME" -Dcgi.query_string="$QUERY_STRING" JasperReportsCGI
