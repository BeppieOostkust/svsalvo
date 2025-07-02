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
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-white">
            <div className="w-full max-w-4xl max-h-[90vh] mx-4 overflow-hidden">
                <Card className="border-2 border-red-500 shadow-xl bg-white">
                    {/* Header */}
                    <CardHeader className="bg-red-50 border-b-2 border-red-200">
                        <div className="flex items-center gap-4">
                            <div className="p-3 bg-red-100 rounded-full">
                                <AlertTriangle className="h-8 w-8 text-red-600" />
                            </div>
                            <div className="flex-1">
                                <CardTitle className="text-3xl text-red-900 mb-2">
                                    Acceptatie Vereist
                                </CardTitle>
                                <p className="text-red-700 text-lg">
                                    U moet de nieuwe <strong>{document.document_title}</strong> (versie {document.document_version}) accepteren om door te gaan
                                </p>
                            </div>
                        </div>
                    </CardHeader>
                    
                    <CardContent className="p-0">
                        {/* Document Info */}
                        <div className="bg-blue-50 border-b border-blue-200 p-4">
                            <div className="flex items-center gap-3 text-blue-800">
                                <FileText className="h-5 w-5" />
                                <span className="font-semibold">{document.document_title}</span>
                                <span className="px-3 py-1 bg-blue-200 rounded-full text-sm">
                                    Versie {document.document_version}
                                </span>
                            </div>
                        </div>
                        
                        {/* Document Content */}
                        <div className="max-h-[50vh] overflow-y-auto p-6 bg-gray-50">
                            <div 
                                className="prose max-w-none prose-headings:text-gray-900 prose-p:text-gray-700"
                                dangerouslySetInnerHTML={{ __html: document.document_content }}
                            />
                        </div>
                        
                        {/* Warning Section */}
                        <div className="border-t border-gray-200 p-6 bg-white">
                            <div className="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-lg mb-6">
                                <div className="flex items-start gap-3">
                                    <AlertTriangle className="h-6 w-6 text-amber-600 mt-0.5 flex-shrink-0" />
                                    <div>
                                        <h4 className="font-bold text-amber-900 mb-2 text-lg">
                                            ⚠️ Belangrijke keuze
                                        </h4>
                                        <p className="text-amber-800 leading-relaxed">
                                            <strong>Let op:</strong> Als u deze voorwaarden niet accepteert, wordt uw account 
                                            automatisch geblokkeerd en kunt u niet meer inloggen. U moet dan contact opnemen 
                                            met de beheerder om uw account te deblokkeren.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            {/* Action Buttons */}
                            <div className="flex gap-4 justify-end">
                                <Button
                                    variant="destructive"
                                    onClick={handleDecline}
                                    disabled={isProcessing}
                                    className="min-w-[140px] text-lg py-3 px-6"
                                >
                                    <X className="h-5 w-5 mr-2" />
                                    {isProcessing ? 'Bezig...' : 'Afwijzen'}
                                </Button>
                                
                                <Button
                                    onClick={handleAccept}
                                    disabled={isProcessing}
                                    className="min-w-[140px] text-lg py-3 px-6 bg-green-600 hover:bg-green-700"
                                >
                                    <Check className="h-5 w-5 mr-2" />
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
