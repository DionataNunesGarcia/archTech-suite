import { render, screen, fireEvent } from '@testing-library/react';
import { Button, Card, Badge, Input } from '@/components/ui';

describe('Button', () => {
  it('renders with primary variant by default', () => {
    render(<Button>Click me</Button>);
    expect(screen.getByText('Click me')).toBeInTheDocument();
  });

  it('applies variant styles', () => {
    render(<Button variant="danger">Delete</Button>);
    const btn = screen.getByText('Delete');
    expect(btn.className).toContain('bg-danger');
  });

  it('applies size styles', () => {
    render(<Button size="lg">Large</Button>);
    const btn = screen.getByText('Large');
    expect(btn.className).toContain('text-[12px]');
  });

  it('calls onClick handler', () => {
    const onClick = jest.fn();
    render(<Button onClick={onClick}>Click</Button>);
    fireEvent.click(screen.getByText('Click'));
    expect(onClick).toHaveBeenCalledTimes(1);
  });
});

describe('Card', () => {
  it('renders title and children', () => {
    render(<Card title="Test Card"><p>Content</p></Card>);
    expect(screen.getByText('Test Card')).toBeInTheDocument();
    expect(screen.getByText('Content')).toBeInTheDocument();
  });

  it('renders headerRight', () => {
    render(<Card title="Card" headerRight={<span>Right</span>}>Body</Card>);
    expect(screen.getByText('Right')).toBeInTheDocument();
  });

  it('renders without title', () => {
    render(<Card><p>Only body</p></Card>);
    expect(screen.getByText('Only body')).toBeInTheDocument();
  });
});

describe('Badge', () => {
  it('renders with primary variant by default', () => {
    render(<Badge>Label</Badge>);
    expect(screen.getByText('Label')).toBeInTheDocument();
  });

  it('applies variant styles', () => {
    render(<Badge variant="danger">Erro</Badge>);
    const badge = screen.getByText('Erro');
    expect(badge.className).toContain('bg-danger');
  });
});

describe('Input', () => {
  it('renders with label', () => {
    render(<Input label="Email" name="email" />);
    expect(screen.getByLabelText('Email')).toBeInTheDocument();
  });

  it('shows error message', () => {
    render(<Input label="Name" name="name" error="Campo obrigatório" />);
    expect(screen.getByText('Campo obrigatório')).toBeInTheDocument();
  });

  it('passes through HTML attributes', () => {
    render(<Input name="test" placeholder="Digite aqui" />);
    expect(screen.getByPlaceholderText('Digite aqui')).toBeInTheDocument();
  });
});
