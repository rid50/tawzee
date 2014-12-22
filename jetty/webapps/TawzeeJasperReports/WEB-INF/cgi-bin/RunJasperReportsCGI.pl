#!/usr/bin/perl -w

# use strict;
# use warnings;

# BEGIN {
#  use CGI::Carp qw(carpout);
#  open(LOG, ">>/var/www/html/cgi-log.log") or
#    die("Unable to open cgi-log: $!\n");
#  carpout(\*LOG);
# }

 use CGI::Carp qw(fatalsToBrowser carpout);
# print "Content-Type: text/html", "\n\n";

# use CGI::Carp qw(fatalsToBrowser);
# die "Bad error here";

# CGI::Carp::SAVEERR;
 
#use CGI::Carp qw(fatalsToBrowser);
#die "Bad error here";


# print "<pre>\n";
# foreach $key (sort keys(%ENV)) {
#   print "$key = $ENV{$key}<p>";
# }
# print "</pre>\n";

# $output = `ls -lart 2>&1`;
# $output = `printenv 2>&1`;
# $output = `env 2>&1`;
# $ENV{JETTY_HOME} = '/home/yarussor/public_html/tawzee/jetty';
# system('/home/yarussor/public_html/tawzee/jetty/bin/jetty.sh start');
# $output = `/home/yarussor/public_html/tawzee/jetty/bin/jetty.sh start 2>&1`;
# system('/home/yarussor/public_html/tawzee/jetty/bin/jetty.sh start');
# system('/home/yarussor/rpm -ivh java-1.7.0-openjdk-devel-1.7.0.71-2.5.3.1.el7_0.x86_64.rpm');
# $output = $ENV{JETTY_HOME};

#system('export DISPLAY=:0');
#$DISPLAY = ':0';
# $ENV{JAVA_HOME} = '/var/www/java-se-7-ri';
# $ENV{JAVA_HOME} = '/var/www/cacao-1.6.1';
 
# $ENV{PATH} = "/var/www/java-se-7-ri/bin:$ENV{PATH}";

$webinf = "/var/www/html/jetty/webapps/TawzeeJasperReports/WEB-INF";
$mlib = "$webinf/lib";

# $ENV{_JAVA_OPTIONS} = '-Xmx256m -XX:+UseSerialGC -verbose:class';
# $ENV{_JAVA_OPTIONS} = "-Xbootclasspath/a:/var/www/html/jetty/webapps/TawzeeJasperReports";
# $ENV{_JAVA_OPTIONS} = "-verbose:class -Xbootclasspath/p:$webinf/classes/myfont.zip:$mlib/commons-logging-1.1.3.jar";
# -verbose:class -Xbootclasspath/p:$webinf/classes/java.zip:$webinf/classes/gnu.zip
# -verbose:class -Xbootclasspath/p:$webinf/classes/jdk-font.zip:$mlib/commons-logging-1.1.3.jar 

# $output = `env 2>&1`;
# -Djava.library.path=/var/www/jamvm-1.5.4/lib/amd64
# $ENV{LD_LIBRARY_PATH} = "/usr/lib/jvm/java-1.7.0-openjdk-1.7.0.45.x86_64/jre/lib/amd64";
# $ENV{PATH} = "/var/www/java-se-7-ri/bin";
 $ENV{PATH} = "/var/www/jamvm-2.0.0/bin";
# $ENV{PATH} = "/var/www/cacao-1.6.1/bin";
 
# -Dgnu.classpath.boot.library.path=/var/www/jamvm-1.5.4/lib/classpath:/usr/lib/jvm/java-1.7.0-openjdk-1.7.0.45.x86_64/jre/lib/amd64 
# system("jamvm -classpath $webinf/classes:$mlib/arial-font-extention.jar GraphicsEnvironmentTest 2>&1");
# system("cacao -classpath $webinf/classes:$mlib/arial-font-extention.jar GraphicsEnvironmentTest 2>&1");

# system("java -Xbootclasspath/p:$webinf/classes/java.zip:$webinf/classes/sun.zip -Xmx256m -XX:+UseSerialGC -cp $webinf/classes:$mlib/* " . ' -Dcgi.server_name="$SERVER_NAME" -Dcgi.query_string="$QUERY_STRING" JasperReportsCGI 2>&1');


system("java -cp $webinf/classes:$mlib/*" . ' -Dcgi.server_name="$SERVER_NAME" -Dcgi.query_string="$QUERY_STRING" JasperReportsCGI 2>&1');

 
# system("java -Xbootclasspath/p:$webinf/classes/java.zip:$webinf/classes/sun.zip -Xmx256m -XX:+UseSerialGC -cp $webinf/classes:$mlib/log4j-1.2.17.jar:$mlib/arial-font-extention.jar:$mlib/xml-apis.jar:$mlib/xercesImpl.jar:$mlib/jasperreports-5.6.1.jar:$mlib/org.eclipse.jdt.core-3.8.2.v20130121.jar:$mlib/commons-beanutils-1.9.1.jar:$mlib/commons-collections-3.2.1.jar:$mlib/commons-digester-2.1.jar:$mlib/commons-logging-1.1.3.jar:$mlib/mysql-connector-java-5.1.29-bin.jar:$mlib/httpclient-4.3.5.jar:$mlib/httpcore-4.3.2.jar" . ' -Dcgi.server_name="$SERVER_NAME" -Dcgi.query_string="$QUERY_STRING" -Djava.awt.headless=false JasperReportsCGI 2>&1');
# system("jamvm -Dgnu.classpath.boot.library.path=/var/www/jamvm-1.5.4/lib/classpath:/usr/lib/jvm/java-1.7.0-openjdk-1.7.0.45.x86_64/jre/lib/amd64 -Xbootclasspath/p:$webinf/classes/java.zip:$webinf/classes/sun.zip -cp $webinf/classes:$mlib/log4j-1.2.17.jar:$mlib/arial-font-extention.jar:$mlib/xml-apis.jar:$mlib/xercesImpl.jar:$mlib/jasperreports-5.6.1.jar:$mlib/org.eclipse.jdt.core-3.8.2.v20130121.jar:$mlib/commons-beanutils-1.9.1.jar:$mlib/commons-collections-3.2.1.jar:$mlib/commons-digester-2.1.jar:$mlib/commons-logging-1.1.3.jar:$mlib/mysql-connector-java-5.1.29-bin.jar:$mlib/httpclient-4.3.5.jar:$mlib/httpcore-4.3.2.jar" . ' -Dcgi.server_name="$SERVER_NAME" -Dcgi.query_string="$QUERY_STRING" -Djava.awt.headless=false JasperReportsCGI 2>&1');

# system("cacao -Dgnu.classpath.boot.library.path=/var/www/cacao-1.6.1/lib/classpath:/usr/lib/jvm/java-1.7.0-openjdk-1.7.0.45.x86_64/jre/lib/amd64 -Xbootclasspath/p:$webinf/classes/java.zip:$webinf/classes/sun.zip -cp $webinf/classes:$mlib/log4j-1.2.17.jar:$mlib/arial-font-extention.jar:$mlib/xml-apis.jar:$mlib/xercesImpl.jar:$mlib/jasperreports-5.6.1.jar:$mlib/org.eclipse.jdt.core-3.8.2.v20130121.jar:$mlib/commons-beanutils-1.9.1.jar:$mlib/commons-collections-3.2.1.jar:$mlib/commons-digester-2.1.jar:$mlib/commons-logging-1.1.3.jar:$mlib/mysql-connector-java-5.1.29-bin.jar:$mlib/httpclient-4.3.5.jar:$mlib/httpcore-4.3.2.jar" . ' -Dcgi.server_name="$SERVER_NAME" -Dcgi.query_string="$QUERY_STRING" -Djava.awt.headless=false JasperReportsCGI 2>&1');
# system("cacao -Xbootclasspath/p:$webinf/classes/java.zip:$webinf/classes/sun.zip -cp $webinf/classes:$mlib/log4j-1.2.17.jar:$mlib/arial-font-extention.jar:$mlib/xml-apis.jar:$mlib/xercesImpl.jar:$mlib/jasperreports-5.6.1.jar:$mlib/org.eclipse.jdt.core-3.8.2.v20130121.jar:$mlib/commons-beanutils-1.9.1.jar:$mlib/commons-collections-3.2.1.jar:$mlib/commons-digester-2.1.jar:$mlib/commons-logging-1.1.3.jar:$mlib/mysql-connector-java-5.1.29-bin.jar:$mlib/httpclient-4.3.5.jar:$mlib/httpcore-4.3.2.jar" . ' -Dcgi.server_name="$SERVER_NAME" -Dcgi.query_string="$QUERY_STRING" -Djava.awt.headless=false JasperReportsCGI 2>&1');

# system('java  -d64 -Xmx256M -XX:MaxPermSize=256m -XX:PermSize=256m -XX:+UseSerialGC -cp /var/www/html/jetty/webapps/TawzeeJasperReports/WEB-INF/lib/*:/var/www/html/jetty/webapps/TawzeeJasperReports/WEB-INF/classes -Dcgi.server_name="$SERVER_NAME" -Dcgi.query_string="$QUERY_STRING" JasperReportsCGI 2>&1');
# system('/var/www/java-se-7-ri/bin/javac /var/www/html/jetty/webapps/TawzeeJasperReports/WEB-INF/src/JasperReportsCGI.java -Xlint:unchecked -Xlint:deprecation -d /var/www/html/jetty/webapps/TawzeeJasperReports/WEB-INF/classes');

# system('jamvm -version 2>&1');
# system('java -client -d32 -Xss256k -Xms128m -Xmx128m -XX:MaxPermSize=128m -XX:PermSize=128m -XX:+UseSerialGC -version 2>&1');
# $output = `java -d64 -Xss256k -Xms128m -Xmx128m -XX:MaxPermSize=128m -XX:PermSize=128m -XX:+UseSerialGC -version 2>&1`;
# $output = `java -version 2>&1`;
# $output = `ulimit -a 2>&1`;


# system('/home/yarussor/jdk-7-ea-bin-b125-linux-i586-13_jan_2011.bin');
# $output = `/home/yarussor/jdk-7-ea-bin-b125-linux-i586-13_jan_2011.bin 2>&1`;

# =pod

$html = qq{Content-Type: text/html

$output
};

print $html;

# =cut