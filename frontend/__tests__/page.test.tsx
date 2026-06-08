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

  it('renders the heading', async () => {
    getHealth.mockResolvedValue({ status: 'ok', database: 'ok', timestamp: Date.now() });
    const PageComponent = await Page();
    render(PageComponent);
    expect(screen.getByText('ArchTech Suite')).toBeInTheDocument();
  });

  it('renders the description', async () => {
    getHealth.mockResolvedValue({ status: 'ok', database: 'ok', timestamp: Date.now() });
    const PageComponent = await Page();
    render(PageComponent);
    expect(screen.getByText(/AI-powered platform for architecture firms/i)).toBeInTheDocument();
  });

  it('shows backend online when health is ok', async () => {
    getHealth.mockResolvedValue({ status: 'ok', database: 'ok', timestamp: Date.now() });
    const PageComponent = await Page();
    render(PageComponent);
    expect(screen.getByText(/Backend:/)).toBeInTheDocument();
    expect(screen.getByText(/✅ Online/)).toBeInTheDocument();
  });

  it('does not show health card when fetch fails', async () => {
    getHealth.mockRejectedValue(new Error('Network error'));
    const PageComponent = await Page();
    render(PageComponent);
    expect(screen.queryByText(/Backend:/)).not.toBeInTheDocument();
  });
});
