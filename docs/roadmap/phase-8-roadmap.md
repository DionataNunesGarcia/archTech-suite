# Phase 8 — Roadmap Proposal

## Based on UAT Feedback & Business Metrics (June 2026)

### Strategic Themes

| Theme                        | Priority | Investment |
| ---------------------------- | -------- | ---------- |
| AI Agent Autonomy & Accuracy | High     | 3 sprints  |
| Mobile Experience (PWA)      | High     | 2 sprints  |
| Advanced Analytics & BI      | Medium   | 2 sprints  |
| Multi-tenant & White-label   | Medium   | 3 sprints  |
| Third-party Integrations     | Low      | 2 sprints  |

### Proposed Epics

#### Epic 1: AI Agent Improvements (Sprint 8-10)

- Fine-tune GPT-4o-mini models for high-volume agents (lead scoring, document classification)
- Implement human-in-the-loop dashboard for AI quality monitoring
- Add A/B testing framework for prompt versions
- Reduce AI latency P90 from 5s to 2s via model optimization

#### Epic 2: Mobile PWA Enhancements (Sprint 8-9)

- Offline-first mode for field supervision (Obras squad)
- Push notification improvements with rich media
- Biometric authentication (Face ID / fingerprint)
- Camera integration for SiteChecklist photos

#### Epic 3: BI & Advanced Analytics (Sprint 10-11)

- Executive dashboard with cross-squad KPIs
- Automated weekly PDF reports via email
- Custom report builder UI
- Export to Excel/PDF with charts

#### Epic 4: Multi-tenant & White-label (Sprint 11-13)

- Tenant isolation at database level
- Custom domain per tenant
- White-label UI (logo, colors, custom CSS)
- Tenant-specific feature flags

#### Epic 5: Integrations (Sprint 12-13)

- Google Drive / Dropbox document sync
- Slack / Teams deeper integration (interactive messages)
- QuickBooks / Conta Azul financial sync
- BIM 360 / Autodesk integration

### Resource Estimates

| Epic                  | Backend | Frontend | AI  | QA  | Total         |
| --------------------- | ------- | -------- | --- | --- | ------------- |
| AI Agent Improvements | 2       | 1        | 3   | 1   | 7 dev-sprints |
| Mobile PWA            | 1       | 3        | 0   | 1   | 5 dev-sprints |
| BI & Analytics        | 2       | 2        | 0   | 1   | 5 dev-sprints |
| Multi-tenant          | 3       | 2        | 0   | 1   | 6 dev-sprints |
| Integrations          | 2       | 1        | 1   | 1   | 5 dev-sprints |

### Go/No-Go Criteria for Phase 8 Start

- [ ] Phase 7 SLOs achieved for 7 consecutive days
- [ ] Zero P1 incidents in first 2 weeks after Phase 7 launch
- [ ] UAT approval rate maintained at ≥ 80%
- [ ] Budget for Phase 8 approved by stakeholders
- [ ] At least 2 squads staffed and available

### Risk Assessment

| Risk                             | Likelihood | Impact | Mitigation                                               |
| -------------------------------- | ---------- | ------ | -------------------------------------------------------- |
| AI cost overruns                 | Medium     | High   | Fine-tune smaller models, set hard budget caps per squad |
| Tenant complexity                | Low        | High   | Start with single-tenant, validate before multi-tenant   |
| Mobile development velocity      | Medium     | Medium | Reuse existing PWA components, progressive enhancement   |
| Stakeholder availability for UAT | Medium     | Medium | Schedule UAT sessions 2 weeks in advance                 |
