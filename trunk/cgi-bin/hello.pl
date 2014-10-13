#!/usr/bin/perl -w

$html = qq{Content-Type: text/html

<HTML>
<HEAD>
<TITLE>Hello World</TITLE>
</HEAD>
<BODY>
<H4>Hello World</H4>
<P>
Your IP Address is $ENV{REMOTE_ADDR}
<P>
<H5>Have a "nice" day</H5>
<p>Name:<input type="text" size="20" name="txt_name" value="Roman">
</BODY>
</HTML>};

print $html;
