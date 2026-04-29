import { Head, Link, usePage } from '@inertiajs/react';

interface PlanningItem {
    id: number;
    week_number: number;
    discipline: 'pistool' | 'geweer';
    discipline_label: string;
    day_of_week: string;
    day_of_week_label: string;
    is_open: boolean;
    start_time: string | null;
    end_time: string | null;
    notes?: string | null;
}

interface QuarterGroup {
    quarter_label: string;
    items: PlanningItem[];
}

interface PageProps {
    quarters: QuarterGroup[];
    [key: string]: unknown;
}

export default function BaanplanningIndex() {
    const { quarters } = usePage<PageProps>().props;

    return (
        <>
            <Head title="Baanplanning" />

            <header className="bg-white shadow-sm">
                <div className="container mx-auto px-4">
                    <div className="flex items-center justify-between h-16">
                        <Link href={route('dashboard.home')} className="flex items-center hover:opacity-80 transition-opacity">
                            <img src="/images/logo.png" alt="SSV De Moes" className="h-8 w-auto mr-3" />
                            <h1 className="text-xl font-bold text-gray-900">SSV De Moes</h1>
                        </Link>
                        <div className="flex items-center space-x-4">
                            <Link href={route('dashboard.home')} className="text-gray-600 hover:text-green-600 font-medium">
                                Home
                            </Link>
                        </div>
                    </div>
                </div>
            </header>

            <main className="min-h-screen bg-gray-50 py-10">
                <div className="container mx-auto px-4 max-w-5xl">
                    <div className="mb-8">
                        <h2 className="text-3xl font-bold text-gray-900">Baanplanning per kwartaal</h2>
                        <p className="mt-2 text-gray-600">
                            Bekijk hieronder wanneer de schietbaan geopend is. De planning wordt elk kwartaal bijgehouden.
                        </p>
                    </div>

                    {quarters.length === 0 ? (
                        <div className="bg-white rounded-lg shadow p-8 text-center text-gray-600">
                            Er zijn op dit moment nog geen openingstijden ingepland.
                        </div>
                    ) : (
                        <div className="space-y-8">
                            {quarters.map((quarter) => (
                                <section key={quarter.quarter_label} className="bg-white rounded-lg shadow">
                                    <div className="px-6 py-4 border-b border-gray-100">
                                        <h3 className="text-xl font-semibold text-gray-900">{quarter.quarter_label}</h3>
                                    </div>
                                    <div className="divide-y divide-gray-100">
                                        {quarter.items.map((item) => (
                                            <article key={item.id} className="px-6 py-4">
                                                <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                                                    <div>
                                                        <h4 className="font-semibold text-gray-900">Week {item.week_number}</h4>
                                                        <p className="text-sm text-gray-600">{item.discipline_label} - {item.day_of_week_label}</p>
                                                    </div>
                                                    {item.is_open ? (
                                                        <div className="inline-flex items-center rounded-md bg-green-100 text-green-800 px-3 py-1 text-sm font-medium">
                                                            Open: {item.start_time?.slice(0, 5)} - {item.end_time?.slice(0, 5)}
                                                        </div>
                                                    ) : (
                                                        <div className="inline-flex items-center rounded-md bg-gray-100 text-gray-700 px-3 py-1 text-sm font-medium">
                                                            Gesloten
                                                        </div>
                                                    )}
                                                </div>
                                                {item.notes && (
                                                    <p className="mt-2 text-sm text-gray-700">{item.notes}</p>
                                                )}
                                            </article>
                                        ))}
                                    </div>
                                </section>
                            ))}
                        </div>
                    )}
                </div>
            </main>
        </>
    );
}
