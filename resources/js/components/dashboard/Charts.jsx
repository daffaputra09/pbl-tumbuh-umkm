import { useState } from 'react';
import {
    Area,
    AreaChart,
    Bar,
    BarChart,
    CartesianGrid,
    Cell,
    Pie,
    PieChart,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';
import { AnimatedNumber, EmptyState, Legend } from '@/components/dashboard/Panel';
import { formatNumber } from '@/components/dashboard/lib/metrics';
import { cn } from '@/lib/utils';

const AXIS_TICK = { fill: '#64748b', fontSize: 12 };

function formatDusunTick(name) {
    const isNarrowScreen = window.matchMedia('(max-width: 639px)').matches;

    return isNarrowScreen && name.length > 8 ? `${name.slice(0, 7)}…` : name;
}

function ChartTooltip({ active, payload, label, labelFormatter }) {
    if (!active || !payload?.length) {
        return null;
    }

    return (
        <div className="min-w-36 rounded-xl border border-border bg-white/95 px-3 py-2.5 text-xs shadow-lg backdrop-blur">
            <p className="mb-1.5 font-semibold text-ink">{labelFormatter ? labelFormatter(label, payload) : label}</p>
            <ul className="flex flex-col gap-1">
                {payload.map((entry) => (
                    <li key={entry.dataKey} className="flex items-center justify-between gap-4">
                        <span className="flex items-center gap-1.5 text-muted-foreground">
                            <span className="size-2 rounded-full" style={{ backgroundColor: entry.color }} />
                            {entry.name}
                        </span>
                        <span className="font-semibold text-ink tabular-nums">{formatNumber(entry.value)}</span>
                    </li>
                ))}
            </ul>
        </div>
    );
}

export function TrendChart({ data }) {
    const total = data.reduce((sum, month) => sum + month.pendaftar, 0);

    if (total === 0) {
        return <EmptyState className="h-64" message="Belum ada pendaftaran UMKM pada periode dan filter ini." />;
    }

    return (
        <div className="flex h-full flex-col gap-3">
            <Legend
                items={[
                    { label: 'Pendaftar baru', color: '#0f766e' },
                    { label: 'Sudah terverifikasi', color: '#d97706' },
                ]}
            />
            <div className="h-64 sm:h-72">
                <ResponsiveContainer width="100%" height="100%">
                    <AreaChart data={data} margin={{ top: 8, right: 8, bottom: 0, left: -20 }}>
                        <defs>
                            <linearGradient id="trend-registered" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stopColor="#0f766e" stopOpacity={0.28} />
                                <stop offset="100%" stopColor="#0f766e" stopOpacity={0} />
                            </linearGradient>
                            <linearGradient id="trend-verified" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stopColor="#d97706" stopOpacity={0.22} />
                                <stop offset="100%" stopColor="#d97706" stopOpacity={0} />
                            </linearGradient>
                        </defs>
                        <CartesianGrid stroke="#e2e8f0" strokeDasharray="4 4" vertical={false} />
                        <XAxis dataKey="label" tick={AXIS_TICK} tickLine={false} axisLine={false} interval="preserveStartEnd" minTickGap={8} />
                        <YAxis tick={AXIS_TICK} tickLine={false} axisLine={false} allowDecimals={false} width={48} />
                        <Tooltip
                            cursor={{ stroke: '#94a3b8', strokeDasharray: '4 4' }}
                            content={<ChartTooltip labelFormatter={(_, payload) => payload[0]?.payload.longLabel} />}
                        />
                        <Area
                            type="monotone"
                            dataKey="terverifikasi"
                            name="Sudah terverifikasi"
                            stroke="#d97706"
                            strokeWidth={2}
                            strokeDasharray="5 4"
                            fill="url(#trend-verified)"
                            activeDot={{ r: 5, strokeWidth: 2, stroke: '#fff' }}
                        />
                        <Area
                            type="monotone"
                            dataKey="pendaftar"
                            name="Pendaftar baru"
                            stroke="#0f766e"
                            strokeWidth={2.5}
                            fill="url(#trend-registered)"
                            activeDot={{ r: 5, strokeWidth: 2, stroke: '#fff' }}
                        />
                    </AreaChart>
                </ResponsiveContainer>
            </div>
        </div>
    );
}

export function VerificationDonut({ data }) {
    const [activeKey, setActiveKey] = useState(null);
    const total = data.reduce((sum, item) => sum + item.value, 0);
    const activeItem = data.find((item) => item.key === activeKey);

    if (total === 0) {
        return <EmptyState className="h-64" />;
    }

    return (
        <div className="flex flex-col items-center gap-5 sm:flex-row lg:flex-col">
            <div className="relative size-48 shrink-0 sm:size-44 lg:size-48">
                <ResponsiveContainer width="100%" height="100%">
                    <PieChart>
                        <Pie
                            data={data}
                            dataKey="value"
                            nameKey="label"
                            innerRadius="70%"
                            outerRadius="100%"
                            paddingAngle={2}
                            cornerRadius={6}
                            stroke="none"
                            onMouseEnter={(entry) => setActiveKey(entry.key)}
                            onMouseLeave={() => setActiveKey(null)}
                        >
                            {data.map((item) => (
                                <Cell
                                    key={item.key}
                                    fill={item.color}
                                    opacity={activeKey && activeKey !== item.key ? 0.35 : 1}
                                    className="transition-opacity"
                                />
                            ))}
                        </Pie>
                    </PieChart>
                </ResponsiveContainer>
                <div className="pointer-events-none absolute inset-0 flex flex-col items-center justify-center text-center">
                    <AnimatedNumber value={activeItem ? activeItem.value : total} className="text-3xl font-extrabold text-ink" />
                    <span className="text-xs font-medium text-muted-foreground">{activeItem ? activeItem.label : 'Total data'}</span>
                </div>
            </div>

            <ul className="flex w-full flex-col gap-2">
                {data.map((item) => (
                    <li key={item.key}>
                        <button
                            type="button"
                            onMouseEnter={() => setActiveKey(item.key)}
                            onMouseLeave={() => setActiveKey(null)}
                            onFocus={() => setActiveKey(item.key)}
                            onBlur={() => setActiveKey(null)}
                            className={cn(
                                'flex w-full items-center gap-3 rounded-xl border px-3 py-2.5 text-left transition-colors',
                                activeKey === item.key ? 'border-brand-200 bg-brand-50/60' : 'border-transparent bg-slate-50 hover:bg-slate-100',
                            )}
                        >
                            <span className="size-2.5 shrink-0 rounded-full" style={{ backgroundColor: item.color }} />
                            <span className="flex-1 text-sm font-medium text-ink">{item.label}</span>
                            <span className="text-sm font-bold text-ink tabular-nums">{formatNumber(item.value)}</span>
                            <span className="w-10 text-right text-xs text-muted-foreground tabular-nums">{item.share}%</span>
                        </button>
                    </li>
                ))}
            </ul>
        </div>
    );
}

export function DusunChart({ data }) {
    const total = data.reduce((sum, item) => sum + item.total, 0);

    if (total === 0) {
        return <EmptyState className="h-64" />;
    }

    return (
        <div className="flex h-full flex-col gap-3">
            <Legend
                items={[
                    { label: 'Aktif', color: '#0f766e' },
                    { label: 'Tidak aktif', color: '#cbd5e1' },
                ]}
            />
            <div className="h-64">
                <ResponsiveContainer width="100%" height="100%">
                    <BarChart data={data} margin={{ top: 8, right: 4, bottom: 0, left: -20 }} barCategoryGap="28%">
                        <CartesianGrid stroke="#e2e8f0" strokeDasharray="4 4" vertical={false} />
                        <XAxis dataKey="name" tick={AXIS_TICK} tickLine={false} axisLine={false} interval={0} tickFormatter={formatDusunTick} />
                        <YAxis tick={AXIS_TICK} tickLine={false} axisLine={false} allowDecimals={false} width={48} />
                        <Tooltip cursor={{ fill: '#f1f5f9', radius: 8 }} content={<ChartTooltip labelFormatter={(label) => `Dusun ${label}`} />} />
                        <Bar dataKey="aktif" name="Aktif" stackId="dusun" fill="#0f766e" maxBarSize={44} />
                        <Bar dataKey="tidakAktif" name="Tidak aktif" stackId="dusun" fill="#cbd5e1" radius={[8, 8, 0, 0]} maxBarSize={44} />
                    </BarChart>
                </ResponsiveContainer>
            </div>
        </div>
    );
}
