import { cn } from '@/lib/utils';

interface CardProps {
	title?: string;
	children: React.ReactNode;
	className?: string;
	headerClassName?: string;
	bodyClassName?: string;
	headerRight?: React.ReactNode;
}

export function Card({ title, children, className, headerClassName, bodyClassName, headerRight }: CardProps) {
	return (
		<div
			className={cn(
				'card bg-white rounded-md border border-transparent shadow-card transition-all duration-300 mb-6',
				className
			)}
		>
			{title && (
				<div
					className={cn(
						'card-header flex items-center justify-between border-b border-gray-300 px-[25px] py-[25px]',
						headerClassName
					)}
				>
					<h6 className="card-title text-brand-dark text-[16px] font-bold m-0">{title}</h6>
					{headerRight && <div>{headerRight}</div>}
				</div>
			)}
			<div className={cn('card-body p-[25px]', bodyClassName)}>{children}</div>
		</div>
	);
}
