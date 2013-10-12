#!/usr/bin/perl -l

# Varnish 5xx URL finder
# Rootnode http://rootnode.net
#
# Copyright (C) 2009-2011 Marcin Hlybin
# All rights reserved.

use warnings;
use strict;
$|++;

my($url,$host,%seen);
open FH, "varnishlog -c -o TxStatus 5.. |";
print 'Waiting for data...';
while(<FH>) {
        /RxURL\s+c\s(.+)$/ and $url  = $1;
        /Host:\s(.+)$/     and $host = $1;
        if($url && $host) {
                unless($seen{$host.$url}) {
                        print 'http://'.$host.$url;
                        $seen{$host.$url}++;
                }
                undef $url;
                undef $host;
        }
}
