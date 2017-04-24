my $r = Apache2::RequestUtil->request;
$r->content_type("text/html");
#$r->send_http_header;
$r->print("mod_perl rules!");
