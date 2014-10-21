<?php

header( 'Content-Type: ' . feed_content_type( 'rss-http' ) . '; charset=' . get_option( 'blog_charset' ), true );
$frequency  = 1;        // Default '1'. The frequency of RSS updates within the update period.
$duration   = 'hourly'; // Default 'hourly'. Accepts 'hourly', 'daily', 'weekly', 'monthly', 'yearly'.

echo '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?' . '>'; ?>
 
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	<?php do_action( 'rss2_ns' ); ?>
>
 
  <!-- RSS feed defaults -->
	<channel>
		<title><?php bloginfo_rss( 'name' ); wp_title_rss(); ?></title>
		<link><?php bloginfo_rss( 'url' ) ?></link>
		<description><?php bloginfo_rss( 'description' ) ?></description>
		<lastBuildDate><?php echo mysql2date( 'D, d M Y H:i:s +0000', get_lastpostmodified( 'GMT' ), false ); ?></lastBuildDate>
		<language><?php bloginfo_rss( 'language' ); ?></language>
		<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', $duration ); ?></sy:updatePeriod>
		<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', $frequency ); ?></sy:updateFrequency>
		<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
 
		<!-- Feed Logo (optional) -->
		<image>
			<url>http://mysite.com/somelogo.png</url>
			<title>
				<?php bloginfo_rss( 'description' ) ?>
			</title>
			<link><?php bloginfo_rss( 'url' ) ?></link>
		</image>
 
		<?php do_action( 'rss2_head' ); ?>
		
		<!-- Start loop -->
			<item>
				<title>Naslovna Strana</title>
				<guid isPermaLink="false"><?php echo date('Ymd'); ?></guid>
				<author>Naslovna Strana</author>
				<pubDate><?php echo date("D, d M Y 06:00:00 T" ); ?></pubDate>
				<content:encoded>
					<![CDATA[<?php if( function_exists( 'view_naslovna_strana' ) ) {view_naslovna_strana(); } ?>]]>
				</content:encoded>
			</item>
	</channel>
</rss>