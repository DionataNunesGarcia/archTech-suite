import type { Metadata } from 'next';
import './globals.css';

export const metadata: Metadata = {
	title: 'ArchTech Suite',
	description: 'AI-powered platform for architecture firms',
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
	return (
		<html lang="pt-BR">
			<body>{children}</body>
		</html>
	);
}
