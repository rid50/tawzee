#!/usr/local/bin/perl -w

# use strict;
# use warnings;

# BEGIN {
#  use CGI::Carp qw(carpout);
#  open(LOG, ">>/home/yarussor/public_html/cgi-log.log") or
#    die("Unable to open cgi-log: $!\n");
#  carpout(\*LOG);
# }

use CGI::Carp qw(fatalsToBrowser carpout);
# print "Content-Type: text/html", "\n\n";

# use CGI::Carp qw(fatalsToBrowser);
# die "Bad error here";

# CGI::Carp::SAVEERR;

# $output = `ls -lart 2>&1`;
# $output = `printenv 2>&1`;
# $output = `env 2>&1`;
# $output = $ENV{JETTY_HOME};


$output = `ps aux 2>&1`;
#system("kill -KILL  367122");

# $output = "ok";

# $output = `java -d64 -Xss256k -Xms128m -Xmx128m -XX:MaxPermSize=128m -XX:PermSize=128m -XX:+UseSerialGC -version 2>&1`;
# $output = `java -version 2>&1`;
# $output = `ulimit -a 2>&1`;
# $output = `/home/yarussor/jdk-7-ea-bin-b125-linux-i586-13_jan_2011.bin 2>&1`;

#=pod

$html = qq{Content-Type: text/html

$output
};

print $html;

#=cut
