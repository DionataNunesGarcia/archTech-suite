import { cn } from '@/lib/utils';

type ButtonVariant = 'primary' | 'secondary' | 'success' | 'warning' | 'danger' | 'info' | 'light' | 'outline';
type ButtonSize = 'sm' | 'md' | 'lg';

const variantStyles: Record<ButtonVariant, string> = {
	primary: 'bg-primary text-white hover:opacity-90',
	secondary: 'bg-secondary text-white hover:opacity-90',
	success: 'bg-success text-white hover:opacity-90',
	warning: 'bg-warning text-white hover:opacity-90',
	danger: 'bg-danger text-white hover:opacity-90',
	info: 'bg-info text-white hover:opacity-90',
	light: 'bg-primary/10 text-primary hover:bg-primary hover:text-white',
	outline: 'border border-gray-300 text-brand-dark hover:border-primary hover:text-primary',
};

const sizeStyles: Record<ButtonSize, string> = {
	sm: 'text-[10px] py-2 px-3',
	md: 'text-[10px] py-3 px-4',
	lg: 'text-[12px] py-4 px-6',
};

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
	variant?: ButtonVariant;
	size?: ButtonSize;
}

export function Button({ variant = 'primary', size = 'md', className, children, ...props }: ButtonProps) {
	return (
		<button
			className={cn(
				'btn inline-flex items-center justify-center rounded-xs font-bold uppercase tracking-[0.5px] transition-all duration-300',
				variantStyles[variant],
				sizeStyles[size],
				className
			)}
			{...props}
		>
			{children}
		</button>
	);
}
