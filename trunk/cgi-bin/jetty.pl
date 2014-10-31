#!/usr/bin/perl -w
#!/usr/local/bin/perl -w

# BEGIN {
#  use CGI::Carp qw(carpout);
#  open(LOG, ">>/home/yarussor/public_html/cgi-log.log") or
#    die("Unable to open cgi-log: $!\n");
#  carpout(\*LOG);
# }

use CGI::Carp qw(fatalsToBrowser);
#die "Bad error here";

# $output = `ls -lart 2>&1`;
# $output = `printenv 2>&1`;
 $output = `env 2>&1`;
# $ENV{JETTY_HOME} = '/var/www/html/jetty';
# $ENV{JETTY_HOME} = '/home/yarussor/public_html/tawzee/jetty';
# system('/home/yarussor/public_html/tawzee/jetty/bin/jetty.sh start');
# $output = `/var/www/html/jetty/bin/jetty.sh start 2>&1`;
# $output = `/home/yarussor/public_html/tawzee/jetty/bin/jetty.sh start 2>&1`;
# system('/var/www/html/jetty/bin/jetty.sh start');
# system('/home/yarussor/public_html/tawzee/jetty/bin/jetty.sh start');
# $output = $ENV{JETTY_HOME};

# =pod

$html = qq{Content-Type: text/html

$output
};

print $html;

# =cut
