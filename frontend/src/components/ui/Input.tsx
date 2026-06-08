import { cn } from '@/lib/utils';

interface InputProps extends React.InputHTMLAttributes<HTMLInputElement> {
  label?: string;
  error?: string;
}

export function Input({ label, error, className, id, ...props }: InputProps) {
  const inputId = id || props.name;
  return (
    <div className="flex flex-col gap-1">
      {label && (
        <label htmlFor={inputId} className="text-[12px] font-semibold text-brand-dark">
          {label}
        </label>
      )}
      <input
        id={inputId}
        className={cn(
          'text-brand-dark px-[15px] py-[12px] border border-gray-300 rounded-sm text-sm',
          'focus:border-primary focus:outline-none',
          'placeholder:text-gray-500',
          error && 'border-danger',
          className,
        )}
        {...props}
      />
      {error && <span className="text-[11px] text-danger">{error}</span>}
    </div>
  );
}
