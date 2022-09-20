<?php

namespace KirbyHelpers;

use Kirby\Cms\Responder;
use Kirby\Http\Response;
use Kirby\Toolkit\Str;
use Kirby\Toolkit\Xml;

class SiteMeta
{
    public static function robots(): Responder
    {
        $robots = 'User-agent: *' . PHP_EOL;
        $robots .= 'Allow: /' . PHP_EOL;
        $robots .= 'Sitemap: ' . url('sitemap.xml');

        return kirby()
            ->response()
            ->type('text')
            ->body($robots);
    }

    public static function sitemap(): Response
    {
        $kirby = kirby();
        $sitemap = [];
        $cache   = $kirby->cache('pages');
        $cacheId = 'sitemap.xml';
        $sitemap = $cache->get($cacheId);
        $xhtmlSchema = 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xhtml="http://www.w3.org/1999/xhtml" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd http://www.w3.org/1999/xhtml http://www.w3.org/2002/08/xhtml/xhtml1-strict.xsd"';

        if ($sitemap === null) {
            $sitemap[] = '<?xml version="1.0" encoding="UTF-8"?>';
            $sitemap[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . ($kirby->multilang() ? " {$xhtmlSchema}" : '') . '>';

            $excludeTemplates = option('kirby-helpers.sitemap.exclude.templates', []);
            $excludePages     = option('kirby-helpers.sitemap.exclude.pages', []);

            if (is_callable($excludePages)) {
                $excludePages = $excludePages();
            }

            foreach (site()->index() as $item) {
                if (in_array($item->intendedTemplate()->name(), $excludeTemplates)) {
                    continue;
                }

                if (preg_match('!^(?:' . implode('|', $excludePages) . ')$!i', $item->id())) {
                    continue;
                }

                $options = $item->blueprint()->options();
                if (isset($options['sitemap']) && $options['sitemap'] === false) {
                    continue;
                }

                $meta = $item->meta();

                $sitemap[] = '<url>';
                $sitemap[] = '  <loc>' . Xml::encode($item->url()) . '</loc>';
                $sitemap[] = '  <lastmod>' . $item->modified('Y-m-d', 'date') . '</lastmod>';
                $sitemap[] = '  <priority>' . number_format($meta->priority(), 1, '.', '') . '</priority>';

                $changefreq = $meta->changefreq();
                if ($changefreq->isNotEmpty()) {
                    $sitemap[] = '  <changefreq>' . $changefreq . '</changefreq>';
                }

                if ($kirby->multilang()) {
                    foreach ($kirby->languages() as $lang) {
                        $code = $lang->code();
                        $locale = $lang->locale(LC_ALL) ?? $code;
                        $locale = pathinfo($locale, PATHINFO_FILENAME);
                        $locale = Str::slug($locale);

                        $sitemap[] = '  <xhtml:link rel="alternate" hreflang="' . $locale . '" href="' . $item->url($code) . '" />';
                    }
                    $sitemap[] = '  <xhtml:link rel="alternate" hreflang="x-default" href="' . $item->url() . '" />';
                }

                $sitemap[] = '</url>';
            }

            $sitemap[] = '</urlset>';
            $sitemap = implode(PHP_EOL, $sitemap);

            $cache->set($cacheId, $sitemap);
        }

        return new Response($sitemap, 'application/xml');
    }
}
