function getBaseUrl(): string {
  return process.env.NEXT_PUBLIC_DRUPAL_BASE_URL || 'http://archtech.ddev.site:8080';
}

export interface HealthResponse {
  status: string;
  database: string;
  timestamp: number;
}

export interface ApiHealthResponse {
  status: string;
  version: string;
  modules: string[];
  timestamp: number;
}

async function fetchJson<T>(path: string, options?: RequestInit): Promise<T> {
  const url = `${getBaseUrl()}${path}`;
  const res = await fetch(url, {
    ...options,
    headers: {
      Accept: 'application/json',
      ...options?.headers,
    },
    cache: 'no-store',
  });
  if (!res.ok) {
    throw new Error(`API ${res.status}: ${res.statusText}`);
  }
  return res.json();
}

export function getHealth(): Promise<HealthResponse> {
  return fetchJson<HealthResponse>('/health');
}

export function getApiHealth(): Promise<ApiHealthResponse> {
  return fetchJson<ApiHealthResponse>('/api/health');
}
