import { getHealth } from '@/lib/api/client';
import { Card } from '@/components/ui/Card';
import { Badge } from '@/components/ui/Badge';
import { Button } from '@/components/ui/Button';

export default async function Home() {
  let health;
  try {
    health = await getHealth();
  } catch {
    health = null;
  }

  return (
    <div className="min-h-screen bg-body-bg text-body-text" style={{ fontFamily: 'Inter, ui-sans-serif, system-ui, sans-serif' }}>
      {/* Header */}
      <header className="flex items-center justify-between bg-white px-[30px] shadow-sm" style={{ height: 80 }}>
        <div className="flex items-center gap-3">
          <h1 className="text-brand-dark text-[18px] font-bold m-0">ArchTech Suite</h1>
        </div>
        <div className="flex items-center gap-4">
          <Badge variant="success" className="text-[12px] px-3 py-1.5">
            Sistema Online
          </Badge>
        </div>
      </header>

      {/* Page Header */}
      <div className="px-[30px] flex items-center" style={{ minHeight: 65 }}>
        <h5 className="text-brand-dark text-[16px] font-bold m-0">Dashboard</h5>
      </div>

      {/* Content */}
      <main className="px-[30px] pb-[5px]">
        {/* Status Cards */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <Card title="Backend" headerRight={<Badge variant={health?.status === 'ok' ? 'success' : 'danger'}>{health?.status === 'ok' ? 'Online' : 'Offline'}</Badge>}>
            <div className="flex items-center justify-between">
              <div>
                <p className="text-brand-muted text-[13px]">Drupal API</p>
                <p className="text-brand-dark text-[22px] font-bold">v11.3</p>
              </div>
              <div className="flex items-center gap-2">
                <span className="text-[11px] text-brand-muted">JSON:API</span>
                <Badge variant="info">REST</Badge>
              </div>
            </div>
          </Card>

          <Card title="Database" headerRight={<Badge variant={health?.database === 'ok' ? 'success' : 'danger'}>{health?.database === 'ok' ? 'Connected' : 'Disconnected'}</Badge>}>
            <div className="flex items-center justify-between">
              <div>
                <p className="text-brand-muted text-[13px]">PostgreSQL</p>
                <p className="text-brand-dark text-[22px] font-bold">18</p>
              </div>
              <div className="flex items-center gap-2">
                <span className="text-[11px] text-brand-muted">Redis</span>
                <Badge variant="success">Cache</Badge>
              </div>
            </div>
          </Card>

          <Card title="Mensageria" headerRight={<Badge variant="success">Ativo</Badge>}>
            <div className="flex items-center justify-between">
              <div>
                <p className="text-brand-muted text-[13px]">RabbitMQ</p>
                <p className="text-brand-dark text-[22px] font-bold">12 exchanges</p>
              </div>
              <div className="flex items-center gap-2">
                <span className="text-[11px] text-brand-muted">Queues</span>
                <Badge variant="warning">32</Badge>
              </div>
            </div>
          </Card>
        </div>

        {/* Quick Actions */}
        <Card title="Ações Rápidas">
          <div className="flex flex-wrap gap-3">
            <Button variant="primary">Novo Lead</Button>
            <Button variant="success">Nova Proposta</Button>
            <Button variant="info">Criar Projeto</Button>
            <Button variant="light">Ver Orçamento</Button>
            <Button variant="outline">Agendar Reunião</Button>
          </div>
        </Card>

        {/* Squads Overview */}
        <Card title="Squads — Visão Geral">
          <table className="table w-full">
            <thead>
              <tr>
                <th>Squad</th>
                <th>Status</th>
                <th>Última Atividade</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td className="font-semibold">Atendimento</td>
                <td><Badge variant="success">Ativo</Badge></td>
                <td className="text-brand-muted">08/06/2026 16:30</td>
              </tr>
              <tr>
                <td className="font-semibold">Projetos</td>
                <td><Badge variant="success">Ativo</Badge></td>
                <td className="text-brand-muted">08/06/2026 15:45</td>
              </tr>
              <tr>
                <td className="font-semibold">Obras</td>
                <td><Badge variant="warning">Pendente</Badge></td>
                <td className="text-brand-muted">08/06/2026 14:20</td>
              </tr>
              <tr>
                <td className="font-semibold">Suporte</td>
                <td><Badge variant="success">Ativo</Badge></td>
                <td className="text-brand-muted">08/06/2026 16:15</td>
              </tr>
              <tr>
                <td className="font-semibold">Insights</td>
                <td><Badge variant="info">Processando</Badge></td>
                <td className="text-brand-muted">08/06/2026 12:00</td>
              </tr>
              <tr>
                <td className="font-semibold">Financeiro</td>
                <td><Badge variant="success">Ativo</Badge></td>
                <td className="text-brand-muted">08/06/2026 16:10</td>
              </tr>
            </tbody>
          </table>
        </Card>
      </main>
    </div>
  );
}
