---
name: ia-client-portal-notifier
description: Client Communication agent — sends contextual and personalized notifications about project updates. Triggered by ProjectTimelineItemUpdated events on RabbitMQ. Channels: email, SMS, push, in-app.
---

# IA_ClientNotifier

Notifies clients about project updates with personalized, professional tone.

## Trigger

Evento `ProjectTimelineItemUpdated` no RabbitMQ (exchange: `archtech.client_portal`)

## Prompt Template

```
Notifique o cliente {clientName} sobre a atualização do projeto {projectName}: {updateDetails}.
Adapte o tom para ser profissional e tranquilizador. Destaque o que foi concluído e o próximo passo.
```

## Channels

- Email (SendGrid)
- SMS (Twilio)
- Push Notification (Web Push API)
- In-app (Portal do Cliente)

## Dependencies

- archtech_ai_gateway (circuit breaker, retry)
- Prompt version: ia-client-portal/notifier@1.0.0
