#!/bin/bash
# Development Helper - Common tasks
# Usage: ./scripts/dev.sh <command>

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

log_info() { echo -e "${GREEN}[DEV]${NC} $1"; }
log_warn() { echo -e "${YELLOW}[WARN]${NC} $1"; }

COMMAND="${1:-help}"

case "$COMMAND" in
  update|up)
    log_info "Updating project..."
    ddev composer install
    ddev drush updatedb -y
    ddev drush config:import -y
    ddev drush cr
    ddev drush uli
    log_info "Updated!"
    ;;
    
  clean)
    log_info "Cleaning caches..."
    ddev drush cr all
    ddev drush router:rebuild
    log_info "Done!"
    ;;
    
  test)
    log_info "Running tests..."
    ddev phpunit --configuration phpunit.xml.dist
    log_info "Tests complete!"
    ;;
    
  install-recipe)
    RECIPE="${2:-base_ai}"
    log_info "Installing recipe: $RECIPE..."
    ddev drush recipe "../recipes/$RECIPE"
    log_info "Done!"
    ;;
    
  ai)
    shift
    ddev ai "$@"
    ;;
    
  fresh)
    log_info "Fresh install..."
    ddev drush sql-drop -y
    ddev drush site:install standard -y
    ddev drush recipe ../recipes/base_admin
    ddev drush recipe ../recipes/base_i18n
    ddev drush recipe ../recipes/base_seo
    ddev drush recipe ../recipes/base_theme
    ddev drush recipe ../recipes/base_lp
    ddev drush recipe ../recipes/base_ai
    ddev theme-install
    ddev theme-build
    ddev drush cr
    ddev drush uli
    log_info "Fresh install complete!"
    ;;
    
  help|*)
    echo "Development Helper Commands"
    echo ""
    echo "Usage: ./scripts/dev.sh <command>"
    echo ""
    echo "Commands:"
    echo "  update, up         Update (composer, updatedb, config, cache)"
    echo "  clean           Clear all caches"
    echo "  test           Run PHPUnit tests"
    echo "  install-recipe   Install a recipe"
    echo "  ai            Run AI commands"
    echo "  fresh          Fresh install"
    echo ""
    ;;
esac