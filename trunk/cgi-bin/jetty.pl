#!/usr/bin/perl -w

# $output = `ls -lart 2>&1`;
$ENV{JETTY_HOME} = '/var/www/html/jetty';
# system('/home/yarussor/public_html/tawzee/jetty/bin/jetty.sh start');
# $output = `/var/www/html/jetty/bin/jetty.sh start 2>&1`;
system('/var/www/html/jetty/bin/jetty.sh start');
# $output = $ENV{JETTY_HOME};

=pod

$html = qq{Content-Type: text/html

$output
};

print $html;

=cut