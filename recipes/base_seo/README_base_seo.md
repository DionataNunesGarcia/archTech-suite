# 📦 Base SEO Recipe

Complete SEO setup for Drupal including clean URLs, meta tags, redirects, XML sitemap, and schema.org markup.

## 🎯 What This Recipe Does

### ✅ Modules Installed

**Core Modules:**
- `path` - URL path system
- `path_alias` - URL alias management

**SEO Essential Modules:**
- `pathauto` - Automatic URL pattern generation
- `token` - Token replacement system
- `metatag` - Meta tag management
- `metatag_open_graph` - Open Graph tags for social sharing
- `metatag_twitter_cards` - Twitter Card tags
- `redirect` - URL redirect management
- `redirect_404` - 404 error tracking and fixing
- `simple_sitemap` - XML sitemap generation

**Additional Enhancements:**
- `robotstxt` - robots.txt management
- `google_analytics` - Google Analytics integration (optional)
- `schema_metatag` - Schema.org structured data
- `schema_article` - Article schema
- `schema_web_page` - WebPage schema

---

## 🔧 Configurations Included

### 1️⃣ Pathauto (Clean URLs)

**Default URL Patterns:**
```
📝 Blog/Article: /blog/[node:title]
📄 Basic Page:   /[node:title]
📁 Other Content: /[node:content-type]/[node:title]
🏷️ Taxonomy:     /[vocabulary]/[term:name]
👤 User:         /user/[user:name]
```

**Settings:**
- ✅ Automatic alias generation
- ✅ Transliteration enabled (converts special characters)
- ✅ Lowercase URLs
- ✅ Hyphens as separators
- ✅ Auto-create 301 redirects when URLs change
- ✅ Max length: 100 characters

---

### 2️⃣ Metatag (SEO Meta Tags)

**Global Defaults:**
```html
<title>[page-title] | [site-name]</title>
<meta name="description" content="[page-summary]">
<link rel="canonical" href="[page-url]">
```

**Open Graph Tags (Facebook, LinkedIn):**
```html
<meta property="og:site_name" content="[site-name]">
<meta property="og:type" content="website">
<meta property="og:title" content="[page-title]">
<meta property="og:description" content="[page-summary]">
<meta property="og:url" content="[page-url]">
<meta property="og:image" content="[site-logo]">
```

**Twitter Cards:**
```html
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="[page-title]">
<meta name="twitter:description" content="[page-summary]">
```

**Content-Specific Tags:**
- 📝 **Articles**: Article schema with publish/modified dates and author
- 📄 **Pages**: Basic page schema
- 🏷️ **Taxonomy**: Category/tag schema
- 👤 **Users**: `noindex, follow` (don't index profiles)

---

### 3️⃣ Simple Sitemap (XML Sitemap)

**Configuration:**
- 📍 Location: `/sitemap.xml`
- 🔄 Auto-generation: Daily via cron
- 📊 Max URLs per sitemap: 2,000
- 🖼️ Include images: Yes

**Default Priorities:**
```
📝 Articles:  0.8 (weekly updates)
📄 Pages:     0.6 (monthly updates)  
🏷️ Tags:      0.5 (monthly updates)
```

---

### 4️⃣ Redirect (URL Redirects)

**Settings:**
- ✅ Auto-redirect on URL changes
- ✅ 301 (permanent) redirects by default
- ✅ Preserve query strings
- ✅ Route normalization enabled
- ✅ 404 error tracking

---

### 5️⃣ Robots.txt

**Default Content:**
```
User-agent: *
Crawl-delay: 10

# Block admin areas
Disallow: /admin/
Disallow: /user/
Disallow: /node/add/

# Allow sitemap
Sitemap: https://yoursite.com/sitemap.xml
```

---

## 📂 File Structure

```
recipes/base_seo/
└── recipe.yml

web/modules/custom/base_seo/
├── base_seo.info.yml
└── base_seo.install
```

---

## 🚀 Installation

```bash
# Apply the recipe
ddev drush recipe ../recipes/base_seo

# Clear cache
ddev drush cr

# Generate initial sitemap
ddev drush simple-sitemap:generate
```

---

## 🎨 Post-Installation Tasks

### 1. Configure Google Analytics (Optional)
```bash
ddev drush config-set google_analytics.settings account "UA-XXXXXX-X"
```

### 2. Add Custom URL Patterns
Go to: **Configuration → Search and metadata → URL aliases → Patterns**

### 3. Configure Metatags Per Content Type
Go to: **Configuration → Search and metadata → Metatag**

### 4. Set Up 404 Redirects
Go to: **Configuration → Search and metadata → URL redirects → Fix 404 pages**

### 5. Customize robots.txt
Go to: **Configuration → Search and metadata → robots.txt**

---

## 🔍 Verification Checklist

After installation, verify:

- [ ] Clean URLs are working: `/blog/my-article` instead of `/node/123`
- [ ] Meta tags appear in page source: `<meta property="og:title"...>`
- [ ] Sitemap is accessible: `https://yoursite.com/sitemap.xml`
- [ ] robots.txt is accessible: `https://yoursite.com/robots.txt`
- [ ] 301 redirects work when changing URLs
- [ ] Social sharing shows correct preview (Facebook, Twitter, LinkedIn)

---

## 🛠️ Useful Drush Commands

```bash
# Generate sitemap manually
ddev drush simple-sitemap:generate

# Rebuild all URL aliases
ddev drush pathauto:aliases-generate

# View current URL patterns
ddev drush config-get pathauto.pattern.blog

# Export SEO config
ddev drush config-export --destination=../config/seo
```

---

## 📊 Additional Modules You Can Add

```yaml
# In recipe.yml under 'install':
- xmlsitemap              # Alternative to simple_sitemap
- page_title              # Advanced page title control
- linkchecker             # Check broken links
- yoast_seo               # Yoast SEO for Drupal
- schema_metatag_service  # Service schema
- schema_metatag_person   # Person schema
```

---

## 🐛 Troubleshooting

### URLs not generating automatically
```bash
ddev drush config-set pathauto.settings enabled_entity_types.node node
ddev drush pathauto:aliases-generate --all
```

### Sitemap not generating
```bash
ddev drush simple-sitemap:rebuild-queue
ddev drush simple-sitemap:generate
```

### Metatags not appearing
Clear cache and check module dependencies:
```bash
ddev drush cr
ddev drush pml | grep metatag
```

---

## 📚 Documentation Links

- [Pathauto Documentation](https://www.drupal.org/docs/contributed-modules/pathauto)
- [Metatag Documentation](https://www.drupal.org/docs/contributed-modules/metatag)
- [Simple Sitemap Documentation](https://www.drupal.org/docs/contributed-modules/simple-xml-sitemap)
- [Schema.org](https://schema.org/)

---

## 🎯 SEO Best Practices Included

✅ **Clean, readable URLs** - `/blog/how-to-install-drupal` instead of `/node/123`  
✅ **Canonical URLs** - Prevents duplicate content issues  
✅ **Open Graph tags** - Better social media sharing  
✅ **Twitter Cards** - Rich previews on Twitter  
✅ **XML Sitemap** - Helps search engines discover content  
✅ **301 Redirects** - Maintains SEO when URLs change  
✅ **robots.txt** - Controls crawler access  
✅ **Schema.org markup** - Structured data for rich results  
✅ **Mobile-friendly meta tags** - Viewport and responsive tags  

---

## 📝 License

MIT - Feel free to modify and use in your projects.
