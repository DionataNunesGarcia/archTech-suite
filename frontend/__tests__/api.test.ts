import { getHealth, getApiHealth } from '@/lib/api/client';

const mockFetch = jest.fn();
global.fetch = mockFetch;

describe('api/client', () => {
  beforeEach(() => {
    jest.clearAllMocks();
    process.env.NEXT_PUBLIC_DRUPAL_BASE_URL = 'http://example.com';
  });

  describe('getHealth', () => {
    it('returns health data on success', async () => {
      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => ({ status: 'ok', database: 'ok', timestamp: 1000 }),
      });

      const result = await getHealth();
      expect(result.status).toBe('ok');
      expect(result.database).toBe('ok');
      expect(mockFetch).toHaveBeenCalledWith(
        'http://example.com/health',
        expect.objectContaining({ headers: { Accept: 'application/json' } }),
      );
    });

    it('throws on non-ok response', async () => {
      mockFetch.mockResolvedValueOnce({ ok: false, status: 500, statusText: 'Server Error' });
      await expect(getHealth()).rejects.toThrow('API 500: Server Error');
    });
  });

  describe('getApiHealth', () => {
    it('returns api health data', async () => {
      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => ({ status: 'ok', version: '11.0.0', modules: ['system'], timestamp: 1000 }),
      });

      const result = await getApiHealth();
      expect(result.version).toBe('11.0.0');
      expect(result.modules).toContain('system');
    });
  });
});
