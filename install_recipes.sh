#!/bin/bash
# ArchTech Suite Installer
set -euo pipefail

GREEN='\033[0;32m'
YELLOW='\011[1;33m'
CYAN='\033[0;36m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${CYAN}=================================================${NC}"
echo -e "${CYAN}      ArchTech Suite — Recipe Installer          ${NC}"
echo -e "${CYAN}=================================================${NC}\n"

run_drush() {
    ddev drush "$@"
}

install_recipe() {
    local recipe_path=$1
    local description=$2
    echo -e "${CYAN}Recipe: ${YELLOW}$recipe_path${NC}"
    echo -e "Description: $description"
    echo -e "${GREEN}Installing...${NC}"
    run_drush recipe "$recipe_path"
    echo ""
}

echo -e "${CYAN}─── Base Platform ────────────────────────────${NC}"
install_recipe "../recipes/base_core" "Drupal Core base configuration"

echo -e "${CYAN}─── Admin ─────────────────────────────────────${NC}"
install_recipe "../recipes/base_admin" "Admin UI (Gin theme, toolbar)"

echo -e "${CYAN}─── Media ─────────────────────────────────────${NC}"
install_recipe "../recipes/base_media" "Media types and library"

echo -e "${CYAN}─── SEO ───────────────────────────────────────${NC}"
install_recipe "../recipes/base_seo" "SEO tools (metatag, sitemap)"

echo -e "${CYAN}─── AI Core ───────────────────────────────────${NC}"
install_recipe "../recipes/base_ai" "AI Core (OpenAI integration)"

echo -e "${CYAN}─── AI Content ────────────────────────────────${NC}"
install_recipe "../recipes/base_ai_contents" "AI Content automation"

echo -e "${CYAN}─── AI Search ─────────────────────────────────${NC}"
install_recipe "../recipes/base_ai_search" "AI Search (pgvector)"

echo -e "${CYAN}─── Contents ──────────────────────────────────${NC}"
install_recipe "../recipes/base_contents" "Content types"

echo -e "${CYAN}─── ArchTech Bounded Contexts ─────────────────${NC}"
echo ""
echo "Select which ArchTech contexts to install:"
CONTEXTS=()
while true; do
    echo "  1) Client Portal"
    echo "  2) CRM"
    echo "  3) Proposals"
    echo "  4) Financial"
    echo "  5) Technical Library"
    echo "  6) Permit Approval"
    echo "  7) Supplier Management"
    echo "  8) Facilities"
    echo "  9) BIM Digital Twin"
    echo "  0) All contexts"
    echo "  d) Done selecting"
    read -p "Choose: " ctx
    case "$ctx" in
        1) CONTEXTS+=("archtech_client_portal");;
        2) CONTEXTS+=("archtech_crm");;
        3) CONTEXTS+=("archtech_proposals");;
        4) CONTEXTS+=("archtech_financeiro");;
        5) CONTEXTS+=("archtech_library");;
        6) CONTEXTS+=("archtech_permits");;
        7) CONTEXTS+=("archtech_suppliers");;
        8) CONTEXTS+=("archtech_facilities");;
        9) CONTEXTS+=("archtech_bim_twin");;
        0) CONTEXTS=("archtech_client_portal" "archtech_crm" "archtech_proposals" "archtech_financeiro" "archtech_library" "archtech_permits" "archtech_suppliers" "archtech_facilities" "archtech_bim_twin"); break;;
        d) break;;
        *) echo "Invalid option";;
    esac
done

for ctx in "${CONTEXTS[@]}"; do
    if [ -d "../recipes/$ctx" ]; then
        install_recipe "../recipes/$ctx" "ArchTech $ctx"
    fi
done

echo -e "${GREEN}Clearing caches...${NC}"
run_drush cr
echo ""
run_drush uli
echo -e "${CYAN}=================================================${NC}"
echo -e "${GREEN}ArchTech Suite installation complete!${NC}"
echo -e "${CYAN}=================================================${NC}\n"
