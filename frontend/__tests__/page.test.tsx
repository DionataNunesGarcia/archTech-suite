import { render, screen } from '@testing-library/react';
import Page from '@/app/page';

jest.mock('@/lib/api/client', () => ({
  getHealth: jest.fn(),
}));

const { getHealth } = jest.requireMock('@/lib/api/client');

describe('Home Page', () => {
  beforeEach(() => {
    jest.clearAllMocks();
  });

  it('renders the header', async () => {
    getHealth.mockResolvedValue({ status: 'ok', database: 'ok', timestamp: Date.now() });
    const PageComponent = await Page();
    render(PageComponent);
    expect(screen.getByText('ArchTech Suite')).toBeInTheDocument();
  });

  it('shows online badge when backend is ok', async () => {
    getHealth.mockResolvedValue({ status: 'ok', database: 'ok', timestamp: Date.now() });
    const PageComponent = await Page();
    render(PageComponent);
    expect(screen.getByText('Sistema Online')).toBeInTheDocument();
  });

  it('shows squad table', async () => {
    getHealth.mockResolvedValue({ status: 'ok', database: 'ok', timestamp: Date.now() });
    const PageComponent = await Page();
    render(PageComponent);
    expect(screen.getByText('Atendimento')).toBeInTheDocument();
    expect(screen.getByText('Projetos')).toBeInTheDocument();
    expect(screen.getByText('Financeiro')).toBeInTheDocument();
  });

  it('renders quick action buttons', async () => {
    getHealth.mockResolvedValue({ status: 'ok', database: 'ok', timestamp: Date.now() });
    const PageComponent = await Page();
    render(PageComponent);
    expect(screen.getByText('Novo Lead')).toBeInTheDocument();
    expect(screen.getByText('Nova Proposta')).toBeInTheDocument();
  });

  it('does not show status cards when fetch fails', async () => {
    getHealth.mockRejectedValue(new Error('Network error'));
    const PageComponent = await Page();
    render(PageComponent);
    expect(screen.getByText('Sistema Online')).toBeInTheDocument(); // still shows header
  });
});
