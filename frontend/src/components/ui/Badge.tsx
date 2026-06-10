import { cn } from '@/lib/utils';

type BadgeVariant = 'primary' | 'secondary' | 'success' | 'warning' | 'danger' | 'info';

const variantStyles: Record<BadgeVariant, string> = {
	primary: 'bg-primary/10 text-[#253d9a] font-bold',
	secondary: 'bg-secondary/10 text-[#4b5563] font-bold',
	success: 'bg-success/10 text-[#186c39] font-bold',
	warning: 'bg-warning/10 text-[#8a5506] font-bold',
	danger: 'bg-danger/10 text-[#9b2a38] font-bold',
	info: 'bg-info/10 text-[#16665e] font-bold',
};

interface BadgeProps {
	variant?: BadgeVariant;
	children: React.ReactNode;
	className?: string;
}

export function Badge({ variant = 'primary', children, className }: BadgeProps) {
	return (
		<span
			className={cn(
				'badge inline-flex text-[11px] font-semibold px-[6px] py-[5px] rounded-xs',
				variantStyles[variant],
				className
			)}
		>
			{children}
		</span>
	);
}
