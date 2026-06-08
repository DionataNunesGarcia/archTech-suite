# Cloudflare WAF configuration (alternative to AWS WAF)
# Usar este módulo se o frontend estiver atrás do Cloudflare

variable "zone_id" {
  description = "Cloudflare Zone ID"
  type        = string
}

variable "environment" {
  type = string
}

# OWASP CRS ruleset via Cloudflare WAF
resource "cloudflare_ruleset" "owasp_crs" {
  zone_id = var.zone_id
  name    = "ArchTech Suite OWASP CRS - ${var.environment}"
  kind    = "zone"
  phase   = "http_request_firewall_custom"

  rules {
    action = "block"
    action_parameters {
      response {
        status_code = 403
        content     = "Blocked by WAF - OWASP CRS"
      }
    }
    expression  = "(cf.waf.score > 5)"
    description = "Block requests with OWASP CRS score > 5"
    enabled     = true
  }
}

# Rate limiting (DDoS protection)
resource "cloudflare_ruleset" "rate_limit" {
  zone_id = var.zone_id
  name    = "ArchTech Suite Rate Limiting - ${var.environment}"
  kind    = "zone"
  phase   = "http_ratelimit"

  rules {
    action = "block"
    action_parameters {
      response {
        status_code = 429
        content     = "Rate limit exceeded. Try again later."
      }
    }
    expression  = "(http.request.uri.path starts with \"/api/\")"
    description = "API rate limit: 1000 requests per 10 minutes"
    enabled     = true

    ratelimit {
      characteristics = ["cf.unique_visitor_id"]
      period          = 600
      requests_per_period = 1000
      mitigation_timeout = 120
    }
  }
}

# DDoS Layer 7 protection
resource "cloudflare_ruleset" "ddos" {
  zone_id = var.zone_id
  name    = "ArchTech Suite DDoS - ${var.environment}"
  kind    = "zone"
  phase   = "ddos_l7"

  rules {
    action = "block"
    expression  = "(cf.threat_score > 10)"
    description = "Block requests with high threat score"
    enabled     = true
  }
}

# Bot management
resource "cloudflare_ruleset" "bot_management" {
  zone_id = var.zone_id
  name    = "ArchTech Suite Bot Management - ${var.environment}"
  kind    = "zone"
  phase   = "http_request_firewall_managed"

  rules {
    action = "block"
    expression  = "(cf.client.bot)"
    description = "Block known bots on API endpoints"
    enabled     = true
  }
}
