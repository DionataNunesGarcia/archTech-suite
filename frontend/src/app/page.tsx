import { getHealth } from '@/lib/api/client';

export default async function Home() {
  let health;
  try {
    health = await getHealth();
  } catch {
    health = null;
  }

  return (
    <main className="flex min-h-screen flex-col items-center justify-center p-8">
      <h1 className="text-4xl font-bold">ArchTech Suite</h1>
      <p className="mt-4 text-lg text-gray-600">AI-powered platform for architecture firms</p>
      {health && (
        <div className="mt-8 rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm">
          <p><strong>Backend:</strong> {health.status === 'ok' ? '✅ Online' : '❌ Offline'}</p>
          <p><strong>Database:</strong> {health.database === 'ok' ? '✅ Connected' : '❌ Disconnected'}</p>
        </div>
      )}
    </main>
  );
}
