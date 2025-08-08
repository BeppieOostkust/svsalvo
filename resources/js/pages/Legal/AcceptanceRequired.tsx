import { useState } from 'react';
import { router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { AlertTriangle, FileText, X, Check } from 'lucide-react';

interface Document {
    document_id: number;
    document_type: string;
    document_title: string;
    document_version: string;
    document_content: string;
}

interface Props {
    document: Document;
}

export default function AcceptanceRequired({ document }: Props) {
    const [isProcessing, setIsProcessing] = useState(false);

    const handleAccept = () => {
        setIsProcessing(true);
        router.post('/legal/accept', {}, {
            onFinish: () => setIsProcessing(false)
        });
    };

    const handleDecline = () => {
        setIsProcessing(true);
        router.post('/legal/decline', {}, {
            onFinish: () => setIsProcessing(false)
        });
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-white p-4">
            <div className="w-full max-w-4xl h-full max-h-[95vh] flex flex-col overflow-hidden">
                <Card className="border-2 border-red-500 shadow-xl bg-white flex flex-col h-full">
                    {/* Header */}
                    <CardHeader className="bg-red-50 border-b-2 border-red-200 flex-shrink-0">
                        <div className="flex items-center gap-4">
                            <div className="p-3 bg-red-100 rounded-full">
                                <AlertTriangle className="h-6 w-6 sm:h-8 sm:w-8 text-red-600" />
                            </div>
                            <div className="flex-1">
                                <CardTitle className="text-xl sm:text-3xl text-red-900 mb-2">
                                    Acceptatie Vereist
                                </CardTitle>
                                <p className="text-red-700 text-sm sm:text-lg">
                                    U moet de nieuwe <strong>{document.document_title}</strong> (versie {document.document_version}) accepteren om door te gaan
                                </p>
                            </div>
                        </div>
                    </CardHeader>
                    
                    <CardContent className="p-0 flex flex-col flex-1 overflow-hidden">
                        {/* Document Info */}
                        <div className="bg-blue-50 border-b border-blue-200 p-3 sm:p-4 flex-shrink-0">
                            <div className="flex items-center gap-3 text-blue-800">
                                <FileText className="h-4 w-4 sm:h-5 sm:w-5 flex-shrink-0" />
                                <span className="font-semibold text-sm sm:text-base">{document.document_title}</span>
                                <span className="px-2 sm:px-3 py-1 bg-blue-200 rounded-full text-xs sm:text-sm">
                                    Versie {document.document_version}
                                </span>
                            </div>
                        </div>
                        
                        {/* Document Content - Scrollable */}
                        <div className="flex-1 overflow-y-auto p-4 sm:p-6 bg-gray-50">
                            <div 
                                className="prose prose-sm sm:prose max-w-none prose-headings:text-gray-900 prose-p:text-gray-700"
                                dangerouslySetInnerHTML={{ __html: document.document_content }}
                            />
                        </div>
                        
                        {/* Warning Section - Fixed at bottom */}
                        <div className="border-t border-gray-200 p-4 sm:p-6 bg-white flex-shrink-0">
                            <div className="bg-amber-50 border-l-4 border-amber-400 p-3 sm:p-4 rounded-r-lg mb-4 sm:mb-6">
                                <div className="flex items-start gap-3">
                                    <AlertTriangle className="h-5 w-5 sm:h-6 sm:w-6 text-amber-600 mt-0.5 flex-shrink-0" />
                                    <div>
                                        <h4 className="font-bold text-amber-900 mb-2 text-base sm:text-lg">
                                            ⚠️ Belangrijke keuze
                                        </h4>
                                        <p className="text-amber-800 leading-relaxed text-sm sm:text-base">
                                            <strong>Let op:</strong> Als u deze voorwaarden niet accepteert, wordt uw account 
                                            automatisch geblokkeerd en kunt u niet meer inloggen. U moet dan contact opnemen 
                                            met de beheerder om uw account te deblokkeren.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            {/* Action Buttons */}
                            <div className="flex flex-col sm:flex-row gap-3 sm:gap-4 sm:justify-end">
                                <Button
                                    variant="destructive"
                                    onClick={handleDecline}
                                    disabled={isProcessing}
                                    className="w-full sm:min-w-[140px] text-base sm:text-lg py-3 px-6 order-2 sm:order-1"
                                >
                                    <X className="h-4 w-4 sm:h-5 sm:w-5 mr-2" />
                                    {isProcessing ? 'Bezig...' : 'Afwijzen'}
                                </Button>
                                
                                <Button
                                    onClick={handleAccept}
                                    disabled={isProcessing}
                                    className="w-full sm:min-w-[140px] text-base sm:text-lg py-3 px-6 bg-green-600 hover:bg-green-700 order-1 sm:order-2"
                                >
                                    <Check className="h-4 w-4 sm:h-5 sm:w-5 mr-2" />
                                    {isProcessing ? 'Bezig...' : 'Accepteren'}
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    );
}
