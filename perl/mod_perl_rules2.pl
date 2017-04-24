# This does not work with mod_perl 2.0
my $r = shift;
$r->send_http_header('text/plain');
$r->print("mod_perl rules!\n");
