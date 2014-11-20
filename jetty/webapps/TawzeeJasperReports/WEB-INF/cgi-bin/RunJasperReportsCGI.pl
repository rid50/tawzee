#!/usr/local/bin/perl -w

# use strict;
use warnings;

 BEGIN {
  use CGI::Carp qw(carpout);
  open(LOG, ">>/home/yarussor/public_html/cgi-log.log") or
    die("Unable to open cgi-log: $!\n");
  carpout(\*LOG);
 }

#use CGI::Carp qw(fatalsToBrowser);
#die "Bad error here";

# $output = `ls -lart 2>&1`;
# $output = `printenv 2>&1`;
# $output = `env 2>&1`;
# $ENV{JETTY_HOME} = '/home/yarussor/public_html/tawzee/jetty';
# system('/home/yarussor/public_html/tawzee/jetty/bin/jetty.sh start');
# $output = `/home/yarussor/public_html/tawzee/jetty/bin/jetty.sh start 2>&1`;
# system('/home/yarussor/public_html/tawzee/jetty/bin/jetty.sh start');
# system('/home/yarussor/rpm -ivh java-1.7.0-openjdk-devel-1.7.0.71-2.5.3.1.el7_0.x86_64.rpm');
# $output = $ENV{JETTY_HOME};

 $ENV{JAVA_HOME} = '/home/yarussor/java-se-7-ri';
 $ENV{PATH} = "$ENV{PATH}:/home/yarussor/java-se-7-ri/bin";
 $ENV{_JAVA_OPTIONS} = '-Xmx64M -XX:+UseSerialGC';
# $output = `env 2>&1`;

 system('java -cp /home/yarussor/public_html/tawzee/jetty/webapps/TawzeeJasperReports/WEB-INF/classes:/home/yarussor/java-se-7-ri/lib/tools.jar:/home/yarussor/public_html/tawzee/jetty/webapps/TawzeeJasperReports/WEB-INF/lib/* -Dcgi.server_name="$SERVER_NAME" -Dcgi.query_string="$QUERY_STRING" JasperReportsCGI');

# system('javac -cp /home/yarussor/public_html/tawzee/jetty/webapps/TawzeeJasperReports/WEB-INF/classes:/home/yarussor/public_html/tawzee/jetty/webapps/TawzeeJasperReports/WEB-INF/lib/* /home/yarussor/public_html/tawzee/jetty/webapps/TawzeeJasperReports/WEB-INF/src/JasperReportsCGI.java -Xlint:unchecked -Xlint:deprecation -d /home/yarussor/public_html/tawzee/jetty/webapps/TawzeeJasperReports/WEB-INF/classes');

# system('java -version');
# system('java -d64 -Xss128k -Xmx128m -XX:MaxPermSize=128m -XX:PermSize=128m -XX:+UseSerialGC -version');
# $output = `java -d64 -Xss128k -Xmx128m -XX:MaxPermSize=128m -XX:PermSize=128m -XX:+UseSerialGC -version`;
# $output = `java -version`;
# $output = `ulimit -a`;


# system('/home/yarussor/jdk-7-ea-bin-b125-linux-i586-13_jan_2011.bin');
# $output = `/home/yarussor/jdk-7-ea-bin-b125-linux-i586-13_jan_2011.bin 2>&1`;

=pod

$html = qq{Content-Type: text/html

$output
};

print $html;

=cut