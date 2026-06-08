import { render, screen } from '@testing-library/react';
import Page from '@/app/page';

describe('Home Page', () => {
  it('renders the heading', () => {
    render(<Page />);
    expect(screen.getByText('ArchTech Suite')).toBeInTheDocument();
  });

  it('renders the description', () => {
    render(<Page />);
    expect(screen.getByText(/AI-powered platform for architecture firms/i)).toBeInTheDocument();
  });
});
