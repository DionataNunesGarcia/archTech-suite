import { cn, formatDate } from '@/lib/utils';

describe('cn', () => {
  it('joins truthy class names', () => {
    expect(cn('a', 'b', 'c')).toBe('a b c');
  });

  it('filters out falsy values', () => {
    expect(cn('a', false, undefined, null, 'b')).toBe('a b');
  });

  it('returns empty string for no args', () => {
    expect(cn()).toBe('');
  });
});

describe('formatDate', () => {
  it('formats date in pt-BR locale', () => {
    const date = new Date('2026-06-08T12:00:00Z');
    const result = formatDate(date);
    expect(result).toContain('junho');
    expect(result).toContain('2026');
  });
});
