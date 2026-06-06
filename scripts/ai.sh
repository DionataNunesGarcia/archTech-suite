#!/bin/bash
# AI Development Helper Scripts
# Works with both DDEV and Lando

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Detect environment
detect_env() {
    if command -v lando &> /dev/null && [ -n "$LANDO" ]; then
        echo "lando"
    elif command -v ddev &> /dev/null && [ -n "$DDEV" ]; then
        echo "ddev"
    else
        echo "direct"
    fi
}

# Get project root
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

log_info() { echo -e "${GREEN}[INFO]${NC} $1"; }
log_warn() { echo -e "${YELLOW}[WARN]${NC} $1"; }
log_error() { echo -e "${RED}[ERROR]${NC} $1"; }

# Run drush (works in both DDEV and Lando)
run_drush() {
    cd "$PROJECT_ROOT"
    if [ -f "vendor/bin/drush" ]; then
        vendor/bin/drush "$@"
    else
        drush "$@"
    fi
}

# Check DDEV
check_ddev() {
    if ! command -v ddev &> /dev/null; then
        log_error "DDEV not found. Install from https://ddev.com"
    fi
}

# Generate module
ai_generate_module() {
    local NAME="$1"
    if [ -z "$NAME" ]; then
        echo "Usage: $0 new-module <module_name>"
        exit 1
    fi
    
    log_info "Creating module: $NAME"
    
    mkdir -p "$PROJECT_ROOT/web/modules/custom/$NAME/src/Controller"
    mkdir -p "$PROJECT_ROOT/web/modules/custom/$NAME/src/Form"
    mkdir -p "$PROJECT_ROOT/web/modules/custom/$NAME/src/Service"
    
    cat > "$PROJECT_ROOT/web/modules/custom/$NAME/${NAME}.info.yml" <<EOF
name: $(echo $NAME | sed 's/_/ /g' | sed 's/\b\(.\)/\u\1/g')
description: 'Custom module created via AI'
core_version_requirement: ^10 || ^11
type: module
package: Custom
version: 1.0.0
EOF

    touch "$PROJECT_ROOT/web/modules/custom/$NAME/${NAME}.module"
    touch "$PROJECT_ROOT/web/modules/custom/$NAME/${NAME}.routing.yml"
    touch "$PROJECT_ROOT/web/modules/custom/$NAME/${NAME}.services.yml"
    
    log_info "Module created: web/modules/custom/$NAME/"
    log_info "Run: drush en $NAME"
}

# Debug project
ai_debug() {
    log_info "Running debug diagnostics..."
    echo ""
    
    echo "=== Cache ==="
    run_drush cr
    echo ""
    
    echo "=== Routes (top 20) ==="
    run_drush core:route 2>/dev/null | head -20
    echo ""
    
    echo "=== Enabled Modules ==="
    run_drush pm:list --status=enabled 2>/dev/null | grep -E "^  .*AI" | head -10
    echo ""
    
    echo "=== Database Status ==="
    run_drush core:status 2>/dev/null | grep -i "db"
    echo ""
    
    log_info "Debug complete!"
}

# Test AI
ai_test() {
    log_info "Testing AI..."
    
    cd "$PROJECT_ROOT"
    
    if run_drush ai:chat "Say 'AI is working!' in exactly 3 words" --provider=openai 2>/dev/null; then
        log_info "OpenAI working!"
    elif run_drush ai:chat "Say 'AI is working!' in exactly 3 words" --provider=ollama 2>/dev/null; then
        log_info "Ollama working!"
    elif command -v ddev &> /dev/null && ddev describe &>/dev/null 2>&1; then
        ddev drush ai:chat "Say 'AI is working!'" 2>/dev/null || \
        log_error "No AI provider configured"
        log_info "Run: drush config-set ai.settings default_provider openai"
    else
        log_error "No AI provider configured"
        log_info "Run: drush config-set ai.settings default_provider openai"
    fi
}

# Install AI
ai_install() {
    log_info "Installing AI..."
    
    cd "$PROJECT_ROOT"
    
    if [ -d "recipes/base_ai" ]; then
        run_drush recipe ../recipes/base_ai
    fi
    
    log_info "AI recipes installed!"
}

# Status
ai_status() {
    echo "=== AI Status ==="
    echo "Provider: $(run_drush config-get ai.settings default_provider 2>/dev/null || echo 'Not configured')"
    echo ""
    echo "Modules:"
    run_drush pm:list --status=enabled 2>/dev/null | grep -E "^  .*AI" || echo "No AI modules"
}

# Show help
ai_help() {
    echo "AI Development Helper"
    echo ""
    echo "Usage: $0 <command> [args]"
    echo ""
    echo "Commands:"
    echo "  new-module <name>   Create new module"
    echo "  debug              Debug project"
    echo "  test               Test AI connection"
    echo "  install            Install AI"
    echo "  status             AI status"
    echo "  help               Show this help"
    echo ""
}

# Main
COMMAND="${1:-help}"

case "$COMMAND" in
    new-module)
        check_ddev
        ai_generate_module "$2"
        ;;
    debug)
        ai_debug
        ;;
    test)
        ai_test
        ;;
    install)
        ai_install
        ;;
    status)
        ai_status
        ;;
    help|*)
        ai_help
        ;;
esac