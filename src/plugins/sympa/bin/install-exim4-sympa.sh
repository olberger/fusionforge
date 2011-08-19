#! /bin/sh
#
# Configuration files for Exim4 needed by sympa
# Christian Bayle, Roland Mas, debian-sf (GForge for Debian)
# Converted to Exim4 by Guillem Jover
#
# This will prepare configuration changes needed by sympa. Other
# mailing-list management software may need different exim4
# configuration changes.
#
# The principle is to generate files somewhere in /var/lib that will
# later (in postinst) be applied with ucf on real exim4 configuration
# files (in /etc), by gforge-mta-exim4 package.


set -e

if [ $(id -u) != 0 ] ; then
  echo "You must be root to run this, please enter passwd"
  exec su -c "$0 $1"
fi

####
# Handle the three configuration setups

oldforgename=gforge
forgename=fusionforge
packagename=$forgename-plugin-sympa

# The new files which will later be applied with ucf are generated inside that dir, and not directly in /etc
ucf_new_dir=/var/lib/$oldforgename/$packagename/etc

# usual non-split config files
cfg_exim4=/etc/exim4/exim4.conf
cfg_exim4_templ=/etc/exim4/exim4.conf.template

# config file (not necessarily there already) in which to add local macro definitions
# doesn't need to be applied to non-split conf file, as included by it already (if it exists)
cfg_exim4_localmacros=/etc/exim4/exim4.conf.localmacros

# New config file to add complementary system aliases read from /etc/mail/sympa/aliases
# needs to be applied to non-split conf file
cfg_exim4_split_router=/etc/exim4/conf.d/router/450_local-fusionforge_sympa_aliases

#cfg_exim4_main="$cfg_exim4_templ $cfg_exim4_split_main"
cfg_exim4_main="$cfg_exim4_templ"
cfg_exim4_router="$cfg_exim4_templ"

# If there's a /etc/exim4/exim4.conf file, add it to the non-split patch needing files
if [ -e $cfg_exim4 ]; then
  cfg_exim4_main="$cfg_exim4_main $cfg_exim4"
  cfg_exim4_router="$cfg_exim4_router $cfg_exim4"
fi

pattern=$(basename $0).XXXXXX

case "$1" in
  configure-files)

    ####
    # Prepare modified exim4 configuration files

    mkdir -p $ucf_new_dir/exim4

    # Insinuate the support for system pipes already added in
    # /etc/aliases by sympa package (Cf. discussion in
    # http://bugs.debian.org/cgi-bin/bugreport.cgi?bug=169102)

    cat > $ucf_new_dir/exim4/$(basename $cfg_exim4_localmacros).$packagename-new  <<EOF
# BEGIN GFORGE BLOCK -- DO NOT EDIT #
# Activating pipe transport in system_aliases router (pipes in /etc/aliases)
.ifndef SYSTEM_ALIASES_PIPE_TRANSPORT
SYSTEM_ALIASES_PIPE_TRANSPORT = address_pipe
.endif
.ifndef SYSTEM_ALIASES_USER
SYSTEM_ALIASES_USER = sympa
.endif
.ifndef SYSTEM_ALIASES_GROUP
SYSTEM_ALIASES_GROUP = sympa
.endif
# END GFORGE BLOCK #
EOF

    # insinuate our system aliases rules in the routers section for /etc/mail/sympa/aliases

    #TODO : get rid of perl as we don't seem to need it
    perl -e '
require ("/etc/gforge/local.pl") ;

my $gf_block = "# BEGIN GFORGE BLOCK -- DO NOT EDIT #
# Using alias pipe definitions for the Sympa lists in /etc/mail/sympa/aliases
sympa_aliases:
  debug_print = ".chr(34)."R: system_aliases for \$local_part@\$domain".chr(34)."
  driver = redirect
  domains = +local_domains
  allow_fail
  allow_defer
  data = \${lookup{\$local_part}lsearch{/etc/mail/sympa/aliases}}
  user = sympa
  group = sympa
  pipe_transport = address_pipe
# END GFORGE BLOCK #
";

print $gf_block;
' > $cfg_exim4_split_router

    # processing the router definitions for the non-split conf files
    for r in $cfg_exim4_router; do
      echo Processing $r

      #cfg_gforge_router=$r.gforge-new
      cfg_gforge_router=$ucf_new_dir/exim4/$(basename $r).$packagename-new
      tmp1=$(mktemp /tmp/$pattern)

      #cp -a $cfg_gforge_router $tmp1
      cp -a $r $tmp1

      perl -e '
$routerfname = shift ;
open ROUTERS, $routerfname || die $!;
my @gf_block = <ROUTERS>;
close ROUTERS;

while (<>) {
  print;
  last if /^\s*begin\s*routers\s*$/;
};
my $in_gf_block = 0;
my $gf_block_done = 0;
my @line_buf = ();
while (<>) {
  last if /^\s*begin\s*$/;
  if (/^# BEGIN GFORGE BLOCK -- DO NOT EDIT #/) {
    $in_gf_block = 1;
    push @line_buf, @gf_block unless $gf_block_done;
    $gf_block_done = 1;
  };
  push @line_buf, $_ unless $in_gf_block;
  $in_gf_block = 0 if /^# END GFORGE BLOCK #/;
};
push @line_buf, $_;
print @gf_block unless $gf_block_done;
print @line_buf;
while (<>) { print; };
' $cfg_exim4_split_router < $tmp1 > $cfg_gforge_router

      rm $tmp1
    done

  ;;

  configure)
    [ -x /usr/bin/newaliases ] && newaliases
    invoke-rc.d exim4 restart
  ;;

  purge-files)
    tmp1=$(mktemp /tmp/$pattern)

    if [ -f $cfg_exim4_split_router ]
    then
    	mv $cfg_exim4_split_router $cfg_exim4_split_router.gforge-new
    fi

    for r in $cfg_exim4_router; do
      #cfg_gforge_router=$r.gforge-new
      cfg_gforge_router=$ucf_new_dir/exim4/$(basename $r).$packagename-new
      tmp1=$(mktemp /tmp/$pattern)

      cp -a $cfg_gforge_router $tmp1

      # Second, kill our forwarding rules
      perl -e '
while (<>) {
  print;
  last if /^\s*begin\s*routers\s*$/;
};
my $in_gf_block = 0;
while (<>) {
  last if /^\s*begin\s*$/;
  $in_gf_block = 1 if /^# BEGIN GFORGE BLOCK -- DO NOT EDIT #/;
  print unless $in_gf_block;
  $in_gf_block = 0 if /^# END GFORGE BLOCK #/;
};
print;
while (<>) { print; };
' < $tmp1 > $cfg_gforge_router

      rm $tmp1
    done
  ;;

  purge)
  ;;

  *)
    echo "Usage: $0 {configure|configure-files|purge|purge-files}"
    exit 1
  ;;

esac

