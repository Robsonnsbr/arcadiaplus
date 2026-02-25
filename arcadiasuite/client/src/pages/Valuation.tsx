import { BrowserFrame } from "@/components/Browser/BrowserFrame";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger, DialogFooter, DialogDescription } from "@/components/ui/dialog";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { ScrollArea } from "@/components/ui/scroll-area";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { useState } from "react";
import { useAuth } from "@/hooks/use-auth";
import { useToast } from "@/hooks/use-toast";
import { 
  Plus, Building2, TrendingUp, Calculator, PieChart, FileText, 
  BarChart3, DollarSign, Calendar, MoreHorizontal, Eye, Edit, Trash2,
  ChevronRight, Users, Target, Briefcase, Bot, Send, Loader2, Upload, MessageSquare,
  ClipboardCheck, CheckCircle2, Circle, AlertCircle, Sparkles, ChevronDown, Paperclip, Download, X,
  Layers, RefreshCw, ThumbsUp, ThumbsDown, Lightbulb
} from "lucide-react";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
import { Progress } from "@/components/ui/progress";
import { RadarChart, Radar, PolarGrid, PolarAngleAxis, PolarRadiusAxis, ResponsiveContainer, Legend } from "recharts";

interface ValuationProject {
  id: number;
  tenantId: number;
  companyName: string;
  cnpj?: string;
  sector: string;
  businessModel?: string;
  stage: string;
  size: string;
  status?: string;
  consultantId?: string;
  clientUserId?: string;
  clientId?: number;
  valuationRangeMin?: string;
  valuationRangeMax?: string;
  finalValue?: string;
  currency?: string;
  reportUrl?: string;
  notes?: string;
  createdAt: string;
  updatedAt: string;
}

interface CrmClient {
  id: number;
  tenantId: number;
  name: string;
  tradeName?: string;
  cnpj?: string;
  email?: string;
  phone?: string;
  segment?: string;
}

interface ValuationInput {
  id: number;
  projectId: number;
  year: number;
  isProjection?: number;
  revenue?: string;
  ebitda?: string;
  netIncome?: string;
  totalAssets?: string;
  totalEquity?: string;
}

interface ValuationCalculation {
  id: number;
  projectId: number;
  method: string;
  weight?: string;
  enterpriseValue?: string;
  equityValue?: string;
  status?: string;
  calculatedAt: string;
}

interface ChecklistCategory {
  id: number;
  code: string;
  name: string;
  description?: string;
  orderIndex: number;
  icon?: string;
  segmentFilter?: string;
}

interface ChecklistItem {
  id: number;
  categoryId: number;
  code: string;
  title: string;
  description?: string;
  format?: string;
  isRequired?: number;
  orderIndex: number;
  segmentFilter?: string;
  agentPrompt?: string;
}

interface ChecklistProgress {
  id: number;
  projectId: number;
  itemId: number;
  status: string;
  notes?: string;
  documentId?: number;
  dataJson?: string;
  agentAnalysis?: string;
  completedAt?: string;
  completedBy?: string;
}

const sectors = [
  "Tecnologia", "Varejo", "Indústria", "Serviços", "Agronegócio", 
  "Saúde", "Educação", "Financeiro", "Imobiliário", "Logística"
];

const stages = ["Startup", "Growth", "Mature", "Turnaround", "Exit"];
const sizes = ["Micro", "Pequena", "Média", "Grande"];
const statuses = ["draft", "in_progress", "review", "completed", "archived"];

function SectorAnalysisTab({ projectId, sector }: { projectId: number; sector: string }) {
  const sectorBenchmarks: Record<string, { evEbitda: string; evRevenue: string; margin: string }> = {
    "Tecnologia": { evEbitda: "15x - 25x", evRevenue: "3x - 8x", margin: "20% - 40%" },
    "Varejo": { evEbitda: "8x - 12x", evRevenue: "0.5x - 1.5x", margin: "3% - 8%" },
    "Indústria": { evEbitda: "6x - 10x", evRevenue: "0.8x - 2x", margin: "8% - 15%" },
    "Serviços": { evEbitda: "8x - 14x", evRevenue: "1x - 3x", margin: "10% - 20%" },
    "Agronegócio": { evEbitda: "5x - 8x", evRevenue: "0.5x - 1.5x", margin: "5% - 12%" },
    "Saúde": { evEbitda: "10x - 18x", evRevenue: "1.5x - 4x", margin: "10% - 25%" },
    "Educação": { evEbitda: "8x - 15x", evRevenue: "1x - 3x", margin: "15% - 30%" },
    "Financeiro": { evEbitda: "10x - 20x", evRevenue: "2x - 5x", margin: "20% - 35%" },
    "Imobiliário": { evEbitda: "8x - 14x", evRevenue: "1x - 3x", margin: "15% - 25%" },
    "Logística": { evEbitda: "7x - 12x", evRevenue: "0.8x - 2x", margin: "5% - 12%" },
  };
  const benchmark = sectorBenchmarks[sector] || { evEbitda: "8x - 12x", evRevenue: "1x - 2x", margin: "10% - 20%" };
  
  return (
    <Card className="p-4">
      <div className="flex items-center gap-2 mb-4">
        <BarChart3 className="h-5 w-5 text-primary" />
        <h3 className="font-medium">Análise Setorial - {sector}</h3>
      </div>
      <div className="grid grid-cols-3 gap-4 mb-6">
        <Card className="p-3 bg-muted/50">
          <p className="text-xs text-muted-foreground mb-1">EV/EBITDA</p>
          <p className="text-lg font-semibold">{benchmark.evEbitda}</p>
        </Card>
        <Card className="p-3 bg-muted/50">
          <p className="text-xs text-muted-foreground mb-1">EV/Receita</p>
          <p className="text-lg font-semibold">{benchmark.evRevenue}</p>
        </Card>
        <Card className="p-3 bg-muted/50">
          <p className="text-xs text-muted-foreground mb-1">Margem EBITDA</p>
          <p className="text-lg font-semibold">{benchmark.margin}</p>
        </Card>
      </div>
      <div className="text-center py-6 text-muted-foreground">
        <BarChart3 className="h-8 w-8 mx-auto mb-2 opacity-50" />
        <p>Dados de mercado e comparáveis setoriais</p>
        <p className="text-xs">Conecte fontes de dados para análises avançadas</p>
      </div>
    </Card>
  );
}


export default function Valuation() {
  const { user } = useAuth();
  const { toast } = useToast();
  const queryClient = useQueryClient();
  const [selectedProject, setSelectedProject] = useState<ValuationProject | null>(null);
  const [newProjectOpen, setNewProjectOpen] = useState(false);
  const [activeTab, setActiveTab] = useState("overview");
  const [newInputOpen, setNewInputOpen] = useState(false);
  const [showAgentPanel, setShowAgentPanel] = useState(false);
  const [agentMessages, setAgentMessages] = useState<{ role: string; content: string }[]>([]);
  const [agentInput, setAgentInput] = useState("");
  const [agentLoading, setAgentLoading] = useState(false);
  const [importOpen, setImportOpen] = useState(false);
  const [importStep, setImportStep] = useState<"upload" | "analyzing" | "review">("upload");
  const [importFile, setImportFile] = useState<File | null>(null);
  const [importedData, setImportedData] = useState<any[]>([]);
  const [importAnalysis, setImportAnalysis] = useState("");
  const [expandedCategory, setExpandedCategory] = useState<number | null>(null);
  const [selectedChecklistItem, setSelectedChecklistItem] = useState<ChecklistItem | null>(null);
  const [checklistItemNotes, setChecklistItemNotes] = useState("");
  const [checklistAgentLoading, setChecklistAgentLoading] = useState(false);
  const [uploadingItem, setUploadingItem] = useState<number | null>(null);
  const [itemAttachments, setItemAttachments] = useState<Record<number, any[]>>({});
  const [newInput, setNewInput] = useState({
    year: new Date().getFullYear(),
    isProjection: 0,
    revenue: "",
    grossProfit: "",
    ebitda: "",
    ebit: "",
    netIncome: "",
    totalAssets: "",
    totalLiabilities: "",
    totalEquity: "",
    cash: "",
    debt: "",
  });

  const { data: projects = [], isLoading } = useQuery<ValuationProject[]>({
    queryKey: ["/api/valuation/projects"],
    queryFn: async () => {
      const res = await fetch("/api/valuation/projects", { credentials: "include" });
      if (!res.ok) return [];
      return res.json();
    },
  });

  const { data: inputs = [] } = useQuery<ValuationInput[]>({
    queryKey: ["/api/valuation/projects", selectedProject?.id, "inputs"],
    queryFn: async () => {
      if (!selectedProject) return [];
      const res = await fetch(`/api/valuation/projects/${selectedProject.id}/inputs`, { credentials: "include" });
      if (!res.ok) return [];
      return res.json();
    },
    enabled: !!selectedProject,
  });

  const { data: calculations = [] } = useQuery<ValuationCalculation[]>({
    queryKey: ["/api/valuation/projects", selectedProject?.id, "calculations"],
    queryFn: async () => {
      if (!selectedProject) return [];
      const res = await fetch(`/api/valuation/projects/${selectedProject.id}/calculations`, { credentials: "include" });
      if (!res.ok) return [];
      return res.json();
    },
    enabled: !!selectedProject,
  });

  const { data: checklistCategories = [] } = useQuery<ChecklistCategory[]>({
    queryKey: ["/api/valuation/checklist/categories", selectedProject?.sector],
    queryFn: async () => {
      const segmentMap: Record<string, string> = {
        "Tecnologia": "technology",
        "Fintech": "fintech",
        "E-commerce": "ecommerce",
        "Indústria": "industry",
        "Agronegócio": "agro",
      };
      const segment = selectedProject ? segmentMap[selectedProject.sector] || "" : "";
      const res = await fetch(`/api/valuation/checklist/categories?segment=${segment}`, { credentials: "include" });
      if (!res.ok) return [];
      return res.json();
    },
    enabled: !!selectedProject,
  });

  const { data: checklistItems = [] } = useQuery<ChecklistItem[]>({
    queryKey: ["/api/valuation/checklist/items", selectedProject?.sector],
    queryFn: async () => {
      const segmentMap: Record<string, string> = {
        "Tecnologia": "technology",
        "Fintech": "fintech",
        "E-commerce": "ecommerce",
        "Indústria": "industry",
        "Agronegócio": "agro",
      };
      const segment = selectedProject ? segmentMap[selectedProject.sector] || "" : "";
      const res = await fetch(`/api/valuation/checklist/items?segment=${segment}`, { credentials: "include" });
      if (!res.ok) return [];
      return res.json();
    },
    enabled: !!selectedProject,
  });

  const { data: checklistProgress = [], refetch: refetchProgress } = useQuery<ChecklistProgress[]>({
    queryKey: ["/api/valuation/projects", selectedProject?.id, "checklist"],
    queryFn: async () => {
      if (!selectedProject) return [];
      const res = await fetch(`/api/valuation/projects/${selectedProject.id}/checklist`, { credentials: "include" });
      if (!res.ok) return [];
      return res.json();
    },
    enabled: !!selectedProject,
  });

  const [newProject, setNewProject] = useState({
    companyName: "",
    cnpj: "",
    sector: "",
    businessModel: "",
    stage: "Growth",
    size: "Média",
    notes: "",
    clientId: null as number | null,
  });
  const [clientSearch, setClientSearch] = useState("");
  const [showQualification, setShowQualification] = useState(false);
  const [qualificationStep, setQualificationStep] = useState(0);
  const [qualificationAnswers, setQualificationAnswers] = useState<Record<string, string>>({});

  const qualificationQuestions = [
    { id: "objective", question: "Qual é o objetivo principal desta avaliação?", options: ["Venda da empresa", "Captação de investimento", "Fusão/Aquisição", "Planejamento sucessório", "Avaliação interna"] },
    { id: "urgency", question: "Qual a urgência para conclusão?", options: ["Imediata (30 dias)", "Curto prazo (90 dias)", "Médio prazo (6 meses)", "Sem pressa definida"] },
    { id: "data_readiness", question: "Como está a organização dos dados financeiros?", options: ["Totalmente organizados e auditados", "Organizados mas não auditados", "Parcialmente organizados", "Precisam ser levantados"] },
    { id: "previous_valuation", question: "Já foi realizado algum valuation anterior?", options: ["Sim, recentemente", "Sim, há mais de 2 anos", "Nunca foi feito"] },
  ];

  const { data: crmClients = [] } = useQuery<CrmClient[]>({
    queryKey: ["/api/valuation/crm-clients", clientSearch],
    queryFn: async () => {
      const params = clientSearch ? `?search=${encodeURIComponent(clientSearch)}` : "";
      const res = await fetch(`/api/valuation/crm-clients${params}`, { credentials: "include" });
      if (!res.ok) return [];
      return res.json();
    },
  });

  const createProjectMutation = useMutation({
    mutationFn: async (data: typeof newProject) => {
      const res = await fetch("/api/valuation/projects", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
        credentials: "include",
      });
      if (!res.ok) throw new Error("Failed to create project");
      return res.json();
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["/api/valuation/projects"] });
      setNewProjectOpen(false);
      setNewProject({ companyName: "", cnpj: "", sector: "", businessModel: "", stage: "Growth", size: "Média", notes: "", clientId: null });
      setShowQualification(false);
      setQualificationStep(0);
      setQualificationAnswers({});
      toast({ title: "Projeto de valuation criado com sucesso" });
    },
    onError: () => {
      toast({ title: "Erro ao criar projeto", variant: "destructive" });
    },
  });

  const deleteProjectMutation = useMutation({
    mutationFn: async (id: number) => {
      const res = await fetch(`/api/valuation/projects/${id}`, {
        method: "DELETE",
        credentials: "include",
      });
      if (!res.ok) throw new Error("Failed to delete project");
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["/api/valuation/projects"] });
      setSelectedProject(null);
      toast({ title: "Projeto excluído" });
    },
  });

  const createInputMutation = useMutation({
    mutationFn: async (data: typeof newInput) => {
      if (!selectedProject) throw new Error("No project selected");
      const res = await fetch(`/api/valuation/projects/${selectedProject.id}/inputs`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
        credentials: "include",
      });
      if (!res.ok) throw new Error("Failed to create input");
      return res.json();
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["/api/valuation/projects", selectedProject?.id, "inputs"] });
      setNewInputOpen(false);
      setNewInput({ year: new Date().getFullYear(), isProjection: 0, revenue: "", grossProfit: "", ebitda: "", ebit: "", netIncome: "", totalAssets: "", totalLiabilities: "", totalEquity: "", cash: "", debt: "" });
      toast({ title: "Dados financeiros adicionados" });
    },
    onError: () => {
      toast({ title: "Erro ao adicionar dados", variant: "destructive" });
    },
  });

  const sendAgentMessage = async () => {
    if (!agentInput.trim() || !selectedProject) return;
    
    const userMessage = { role: "user", content: agentInput };
    setAgentMessages(prev => [...prev, userMessage]);
    setAgentInput("");
    setAgentLoading(true);
    
    try {
      const context = `Você está auxiliando na avaliação da empresa "${selectedProject.companyName}" (setor: ${selectedProject.sector}, porte: ${selectedProject.size}, estágio: ${selectedProject.stage}). Dados disponíveis: ${inputs.length} anos de dados financeiros.`;
      
      const res = await fetch("/api/agent/chat", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ 
          message: agentInput,
          context: context,
          module: "valuation"
        }),
        credentials: "include",
      });
      
      if (res.ok) {
        const data = await res.json();
        setAgentMessages(prev => [...prev, { role: "assistant", content: data.response || data.message || "Desculpe, não consegui processar sua solicitação." }]);
      } else {
        setAgentMessages(prev => [...prev, { role: "assistant", content: "Erro ao conectar com o assistente. Tente novamente." }]);
      }
    } catch (error) {
      setAgentMessages(prev => [...prev, { role: "assistant", content: "Erro de conexão. Verifique sua internet." }]);
    } finally {
      setAgentLoading(false);
    }
  };

  const handleFileUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file || !selectedProject) return;
    
    setImportFile(file);
    setImportStep("analyzing");
    
    const formData = new FormData();
    formData.append("file", file);
    
    try {
      const res = await fetch(`/api/valuation/projects/${selectedProject.id}/import-financial`, {
        method: "POST",
        body: formData,
        credentials: "include",
      });
      
      if (res.ok) {
        const data = await res.json();
        setImportedData(data.rows || []);
        setImportAnalysis(data.analysis || "");
        setImportStep("review");
      } else {
        toast({ title: "Erro ao processar arquivo", variant: "destructive" });
        setImportStep("upload");
      }
    } catch (error) {
      toast({ title: "Erro ao enviar arquivo", variant: "destructive" });
      setImportStep("upload");
    }
  };

  const saveImportedData = async () => {
    if (!selectedProject || importedData.length === 0) return;
    
    try {
      for (const row of importedData) {
        await fetch(`/api/valuation/projects/${selectedProject.id}/inputs`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(row),
          credentials: "include",
        });
      }
      queryClient.invalidateQueries({ queryKey: ["/api/valuation/projects", selectedProject.id, "inputs"] });
      setImportOpen(false);
      setImportStep("upload");
      setImportFile(null);
      setImportedData([]);
      setImportAnalysis("");
      toast({ title: `${importedData.length} anos de dados importados com sucesso` });
    } catch (error) {
      toast({ title: "Erro ao salvar dados", variant: "destructive" });
    }
  };

  const initializeChecklist = async () => {
    if (!selectedProject) return;
    try {
      await fetch(`/api/valuation/projects/${selectedProject.id}/checklist/initialize`, {
        method: "POST",
        credentials: "include",
      });
      refetchProgress();
      toast({ title: "Checklist inicializado com sucesso" });
    } catch (error) {
      toast({ title: "Erro ao inicializar checklist", variant: "destructive" });
    }
  };

  const updateChecklistItem = async (itemId: number, status: string, notes?: string) => {
    if (!selectedProject) return;
    try {
      await fetch(`/api/valuation/projects/${selectedProject.id}/checklist/${itemId}`, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ status, notes }),
        credentials: "include",
      });
      refetchProgress();
    } catch (error) {
      toast({ title: "Erro ao atualizar item", variant: "destructive" });
    }
  };

  const requestAgentAssist = async (item: ChecklistItem) => {
    if (!selectedProject) return;
    setChecklistAgentLoading(true);
    try {
      const res = await fetch(`/api/valuation/projects/${selectedProject.id}/checklist/${item.id}/agent-assist`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ content: checklistItemNotes }),
        credentials: "include",
      });
      if (res.ok) {
        const data = await res.json();
        refetchProgress();
        toast({ title: "Análise do Agent concluída" });
      }
    } catch (error) {
      toast({ title: "Erro ao solicitar assistência do Agent", variant: "destructive" });
    } finally {
      setChecklistAgentLoading(false);
    }
  };

  const loadAttachments = async (itemId: number) => {
    if (!selectedProject) return;
    try {
      const res = await fetch(`/api/valuation/projects/${selectedProject.id}/checklist/${itemId}/attachments`, {
        credentials: "include",
      });
      if (res.ok) {
        const data = await res.json();
        setItemAttachments(prev => ({ ...prev, [itemId]: data }));
      }
    } catch (error) {
      console.error("Error loading attachments:", error);
    }
  };

  const uploadAttachment = async (itemId: number, file: File) => {
    if (!selectedProject) return;
    setUploadingItem(itemId);
    try {
      const formData = new FormData();
      formData.append("file", file);
      
      const res = await fetch(`/api/valuation/projects/${selectedProject.id}/checklist/${itemId}/attachments`, {
        method: "POST",
        body: formData,
        credentials: "include",
      });
      
      if (res.ok) {
        toast({ title: "Arquivo anexado com sucesso" });
        loadAttachments(itemId);
        refetchProgress();
      } else {
        const error = await res.json();
        toast({ title: error.error || "Erro ao anexar arquivo", variant: "destructive" });
      }
    } catch (error) {
      toast({ title: "Erro ao anexar arquivo", variant: "destructive" });
    } finally {
      setUploadingItem(null);
    }
  };

  const deleteAttachment = async (itemId: number, attachmentId: number) => {
    if (!selectedProject) return;
    try {
      const res = await fetch(`/api/valuation/projects/${selectedProject.id}/checklist/${itemId}/attachments/${attachmentId}`, {
        method: "DELETE",
        credentials: "include",
      });
      
      if (res.ok) {
        toast({ title: "Anexo removido" });
        loadAttachments(itemId);
      }
    } catch (error) {
      toast({ title: "Erro ao remover anexo", variant: "destructive" });
    }
  };

  const getItemProgress = (itemId: number): ChecklistProgress | undefined => {
    return checklistProgress.find(p => p.itemId === itemId);
  };

  const getCategoryProgress = (categoryId: number) => {
    const categoryItems = checklistItems.filter(i => i.categoryId === categoryId);
    const completed = categoryItems.filter(i => getItemProgress(i.id)?.status === "completed").length;
    return { total: categoryItems.length, completed };
  };

  const getOverallProgress = () => {
    if (checklistItems.length === 0) return 0;
    const completed = checklistItems.filter(i => getItemProgress(i.id)?.status === "completed").length;
    return Math.round((completed / checklistItems.length) * 100);
  };

  const statusColors: Record<string, string> = {
    draft: "bg-slate-100 text-slate-600",
    in_progress: "bg-blue-100 text-blue-600",
    review: "bg-yellow-100 text-yellow-700",
    completed: "bg-green-100 text-green-600",
    archived: "bg-gray-100 text-gray-500",
  };

  const statusLabels: Record<string, string> = {
    draft: "Rascunho",
    in_progress: "Em Andamento",
    review: "Em Revisão",
    completed: "Concluído",
    archived: "Arquivado",
  };

  const formatCurrency = (value?: string) => {
    if (!value) return "-";
    const num = parseFloat(value);
    return new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" }).format(num);
  };

  const getProgressPercentage = (status?: string) => {
    const statusProgress: Record<string, number> = {
      draft: 10,
      in_progress: 50,
      review: 80,
      completed: 100,
      archived: 100,
    };
    return statusProgress[status || "draft"] || 0;
  };

  return (
    <BrowserFrame>
      <div className="flex h-full bg-slate-50">
        <aside className="w-72 border-r bg-white flex flex-col">
          <div className="p-4 border-b">
            <h2 className="font-semibold text-lg flex items-center gap-2" data-testid="text-valuation-title">
              <TrendingUp className="h-5 w-5 text-emerald-600" />
              Valuation
            </h2>
            <p className="text-xs text-slate-500 mt-1">Avaliação de Empresas</p>
          </div>

          <ScrollArea className="flex-1">
            <div className="p-2 space-y-1">
              {isLoading ? (
                <div className="p-4 text-center text-slate-500">Carregando...</div>
              ) : projects.length === 0 ? (
                <div className="p-4 text-center text-slate-500">
                  <Building2 className="h-8 w-8 mx-auto mb-2 opacity-50" />
                  <p className="text-sm">Nenhum projeto ainda</p>
                </div>
              ) : (
                projects.map((project) => (
                  <Card
                    key={project.id}
                    className={`p-3 cursor-pointer transition-colors hover:bg-slate-50 ${
                      selectedProject?.id === project.id ? "ring-2 ring-emerald-500 bg-emerald-50" : ""
                    }`}
                    onClick={() => setSelectedProject(project)}
                    data-testid={`card-project-${project.id}`}
                  >
                    <div className="flex items-start justify-between">
                      <div className="flex-1 min-w-0">
                        <h3 className="font-medium text-sm truncate">{project.companyName}</h3>
                        <p className="text-xs text-slate-500">{project.sector}</p>
                        <div className="flex items-center gap-2 mt-2">
                          <Badge variant="secondary" className={`text-xs ${statusColors[project.status || "draft"]}`}>
                            {statusLabels[project.status || "draft"]}
                          </Badge>
                        </div>
                      </div>
                      <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                          <Button variant="ghost" size="icon" className="h-6 w-6">
                            <MoreHorizontal className="h-4 w-4" />
                          </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                          <DropdownMenuItem onClick={() => setSelectedProject(project)}>
                            <Eye className="h-4 w-4 mr-2" /> Visualizar
                          </DropdownMenuItem>
                          <DropdownMenuItem 
                            className="text-red-600"
                            onClick={() => deleteProjectMutation.mutate(project.id)}
                          >
                            <Trash2 className="h-4 w-4 mr-2" /> Excluir
                          </DropdownMenuItem>
                        </DropdownMenuContent>
                      </DropdownMenu>
                    </div>
                  </Card>
                ))
              )}
            </div>
          </ScrollArea>
        </aside>

        <main className="flex-1 flex flex-col overflow-hidden min-h-0">
          {selectedProject ? (
            <>
              <div className="bg-white border-b p-4">
                <div className="flex items-center justify-between">
                  <div>
                    <div className="flex items-center gap-2">
                      <Building2 className="h-5 w-5 text-emerald-600" />
                      <h1 className="text-xl font-semibold" data-testid="text-project-name">{selectedProject.companyName}</h1>
                      <Badge variant="secondary" className={statusColors[selectedProject.status || "draft"]}>
                        {statusLabels[selectedProject.status || "draft"]}
                      </Badge>
                    </div>
                    <p className="text-sm text-slate-500 mt-1">
                      {selectedProject.sector} | {selectedProject.stage} | {selectedProject.size}
                    </p>
                  </div>
                  <div className="flex items-center gap-4">
                    <div className="text-right">
                      <p className="text-xs text-slate-500">Valor Estimado</p>
                      <p className="text-lg font-semibold text-emerald-600">
                        {selectedProject.finalValue ? formatCurrency(selectedProject.finalValue) : 
                         selectedProject.valuationRangeMin && selectedProject.valuationRangeMax ?
                         `${formatCurrency(selectedProject.valuationRangeMin)} - ${formatCurrency(selectedProject.valuationRangeMax)}` :
                         "A calcular"}
                      </p>
                    </div>
                    <div className="w-32">
                      <p className="text-xs text-slate-500 mb-1">Progresso</p>
                      <Progress value={getProgressPercentage(selectedProject.status)} className="h-2" />
                    </div>
                  </div>
                </div>
              </div>

              <Tabs value={activeTab} onValueChange={setActiveTab} className="flex-1 flex flex-col min-h-0">
                <TabsList className="mx-4 mt-4 w-fit">
                  <TabsTrigger value="overview" data-testid="tab-overview">
                    <BarChart3 className="h-4 w-4 mr-2" /> Visão Geral
                  </TabsTrigger>
                  <TabsTrigger value="checklist" data-testid="tab-checklist">
                    <ClipboardCheck className="h-4 w-4 mr-2" /> Checklist
                  </TabsTrigger>
                  <TabsTrigger value="financials" data-testid="tab-financials">
                    <DollarSign className="h-4 w-4 mr-2" /> Dados Financeiros
                  </TabsTrigger>
                  <TabsTrigger value="calculations" data-testid="tab-calculations">
                    <Calculator className="h-4 w-4 mr-2" /> Cálculos
                  </TabsTrigger>
                  <TabsTrigger value="captable" data-testid="tab-captable">
                    <PieChart className="h-4 w-4 mr-2" /> Cap Table
                  </TabsTrigger>
                  <TabsTrigger value="documents" data-testid="tab-documents">
                    <FileText className="h-4 w-4 mr-2" /> Data Room
                  </TabsTrigger>
                  <TabsTrigger value="sector" data-testid="tab-sector">
                    <TrendingUp className="h-4 w-4 mr-2" /> Análise Setorial
                  </TabsTrigger>
                                  </TabsList>

                <div className="flex-1 overflow-auto p-4">
                  <TabsContent value="overview" className="mt-0">
                    <div className="grid grid-cols-3 gap-4">
                      <Card className="p-4">
                        <div className="flex items-center gap-3">
                          <div className="p-2 bg-emerald-100 rounded-lg">
                            <TrendingUp className="h-5 w-5 text-emerald-600" />
                          </div>
                          <div>
                            <p className="text-sm text-slate-500">Enterprise Value</p>
                            <p className="text-xl font-semibold">
                              {calculations.length > 0 
                                ? formatCurrency(calculations[0].enterpriseValue)
                                : "-"}
                            </p>
                          </div>
                        </div>
                      </Card>

                      <Card className="p-4">
                        <div className="flex items-center gap-3">
                          <div className="p-2 bg-blue-100 rounded-lg">
                            <DollarSign className="h-5 w-5 text-blue-600" />
                          </div>
                          <div>
                            <p className="text-sm text-slate-500">Equity Value</p>
                            <p className="text-xl font-semibold">
                              {calculations.length > 0 
                                ? formatCurrency(calculations[0].equityValue)
                                : "-"}
                            </p>
                          </div>
                        </div>
                      </Card>

                      <Card className="p-4">
                        <div className="flex items-center gap-3">
                          <div className="p-2 bg-purple-100 rounded-lg">
                            <Calculator className="h-5 w-5 text-purple-600" />
                          </div>
                          <div>
                            <p className="text-sm text-slate-500">Metodologias</p>
                            <p className="text-xl font-semibold">{calculations.length}</p>
                          </div>
                        </div>
                      </Card>
                    </div>

                    <div className="grid grid-cols-2 gap-4 mt-4">
                      <Card className="p-4">
                        <h3 className="font-medium mb-3 flex items-center gap-2">
                          <Briefcase className="h-4 w-4" /> Informações da Empresa
                        </h3>
                        <div className="space-y-2 text-sm">
                          <div className="flex justify-between">
                            <span className="text-slate-500">CNPJ:</span>
                            <span>{selectedProject.cnpj || "-"}</span>
                          </div>
                          <div className="flex justify-between">
                            <span className="text-slate-500">Setor:</span>
                            <span>{selectedProject.sector}</span>
                          </div>
                          <div className="flex justify-between">
                            <span className="text-slate-500">Modelo:</span>
                            <span>{selectedProject.businessModel || "-"}</span>
                          </div>
                          <div className="flex justify-between">
                            <span className="text-slate-500">Estágio:</span>
                            <span>{selectedProject.stage}</span>
                          </div>
                          <div className="flex justify-between">
                            <span className="text-slate-500">Porte:</span>
                            <span>{selectedProject.size}</span>
                          </div>
                        </div>
                      </Card>

                      <Card className="p-4">
                        <h3 className="font-medium mb-3 flex items-center gap-2">
                          <Target className="h-4 w-4" /> Últimos Resultados
                        </h3>
                        {inputs.length > 0 ? (
                          <div className="space-y-2 text-sm">
                            {inputs.slice(0, 3).map((input) => (
                              <div key={input.id} className="flex justify-between">
                                <span className="text-slate-500">{input.year}:</span>
                                <span>Receita: {formatCurrency(input.revenue)}</span>
                              </div>
                            ))}
                          </div>
                        ) : (
                          <p className="text-sm text-slate-500">Nenhum dado financeiro cadastrado</p>
                        )}
                      </Card>
                    </div>

                    {selectedProject.notes && (
                      <Card className="p-4 mt-4">
                        <h3 className="font-medium mb-2">Observações</h3>
                        <p className="text-sm text-slate-600">{selectedProject.notes}</p>
                      </Card>
                    )}
                  </TabsContent>

                  <TabsContent value="checklist" className="mt-0">
                    <div className="space-y-4">
                      <div className="flex items-center justify-between">
                        <div>
                          <h3 className="font-medium text-lg">Checklist de Dados para Valuation</h3>
                          <p className="text-sm text-slate-500">Complete os itens abaixo com assistência do Agent</p>
                        </div>
                        <div className="flex items-center gap-4">
                          <div className="text-right">
                            <p className="text-2xl font-bold text-emerald-600">{getOverallProgress()}%</p>
                            <p className="text-xs text-slate-500">Completo</p>
                          </div>
                          {checklistProgress.length === 0 && (
                            <Button onClick={initializeChecklist} data-testid="button-init-checklist">
                              Iniciar Checklist
                            </Button>
                          )}
                        </div>
                      </div>

                      <Progress value={getOverallProgress()} className="h-2" />

                      <div className="space-y-3">
                        {checklistCategories.map((category) => {
                          const progress = getCategoryProgress(category.id);
                          const isExpanded = expandedCategory === category.id;
                          const categoryItems = checklistItems.filter(i => i.categoryId === category.id);

                          return (
                            <Card key={category.id} className="overflow-hidden" data-testid={`card-category-${category.id}`}>
                              <div 
                                className="p-4 flex items-center justify-between cursor-pointer hover:bg-slate-50"
                                onClick={() => {
                                  const newExpanded = isExpanded ? null : category.id;
                                  setExpandedCategory(newExpanded);
                                  if (newExpanded) {
                                    categoryItems.forEach(i => loadAttachments(i.id));
                                  }
                                }}
                                data-testid={`button-expand-category-${category.id}`}
                              >
                                <div className="flex items-center gap-3">
                                  <div className="p-2 bg-emerald-100 rounded-lg">
                                    <ClipboardCheck className="h-5 w-5 text-emerald-600" />
                                  </div>
                                  <div>
                                    <h4 className="font-medium">{category.name}</h4>
                                    <p className="text-xs text-slate-500">{category.description}</p>
                                  </div>
                                </div>
                                <div className="flex items-center gap-4">
                                  <div className="text-right">
                                    <span className="text-sm font-medium">{progress.completed}/{progress.total}</span>
                                    <p className="text-xs text-slate-500">itens</p>
                                  </div>
                                  <ChevronDown className={`h-5 w-5 transition-transform ${isExpanded ? "rotate-180" : ""}`} />
                                </div>
                              </div>

                              {isExpanded && (
                                <div className="border-t bg-slate-50 p-4 space-y-2">
                                  {categoryItems.map((item) => {
                                    const itemProgress = getItemProgress(item.id);
                                    const status = itemProgress?.status || "pending";
                                    
                                    return (
                                      <div 
                                        key={item.id} 
                                        className="bg-white rounded-lg p-4 border"
                                        data-testid={`card-checklist-item-${item.id}`}
                                      >
                                        <div className="flex items-start justify-between">
                                          <div className="flex items-start gap-3">
                                            <button 
                                              onClick={() => updateChecklistItem(item.id, status === "completed" ? "pending" : "completed")}
                                              className="mt-0.5"
                                              data-testid={`button-toggle-status-${item.id}`}
                                            >
                                              {status === "completed" ? (
                                                <CheckCircle2 className="h-5 w-5 text-emerald-600" />
                                              ) : status === "in_progress" ? (
                                                <AlertCircle className="h-5 w-5 text-amber-500" />
                                              ) : (
                                                <Circle className="h-5 w-5 text-slate-300" />
                                              )}
                                            </button>
                                            <div>
                                              <div className="flex items-center gap-2">
                                                <span className="text-xs font-mono text-slate-400">{item.code}</span>
                                                <h5 className={`font-medium ${status === "completed" ? "line-through text-slate-400" : ""}`}>
                                                  {item.title}
                                                </h5>
                                                {item.isRequired ? (
                                                  <Badge variant="outline" className="text-xs">Obrigatório</Badge>
                                                ) : null}
                                              </div>
                                              {item.description && (
                                                <p className="text-sm text-slate-500 mt-1">{item.description}</p>
                                              )}
                                              {item.format && (
                                                <p className="text-xs text-slate-400 mt-1">Formato: {item.format}</p>
                                              )}
                                              
                                              {/* Attachments Section */}
                                              {itemAttachments[item.id]?.length > 0 && (
                                                <div className="mt-3 space-y-1">
                                                  <p className="text-xs font-medium text-slate-500">Anexos:</p>
                                                  <div className="flex flex-wrap gap-2">
                                                    {itemAttachments[item.id].map((att: any) => (
                                                      <div 
                                                        key={att.id} 
                                                        className="flex items-center gap-1 bg-slate-100 rounded px-2 py-1 text-xs"
                                                        data-testid={`attachment-${att.id}`}
                                                      >
                                                        <Paperclip className="h-3 w-3" />
                                                        <a 
                                                          href={`/api/valuation/checklist/attachments/${att.id}/download`}
                                                          className="text-blue-600 hover:underline max-w-32 truncate"
                                                          title={att.originalName}
                                                        >
                                                          {att.originalName}
                                                        </a>
                                                        <button 
                                                          onClick={() => deleteAttachment(item.id, att.id)}
                                                          className="text-red-500 hover:text-red-700 ml-1"
                                                          data-testid={`button-delete-attachment-${att.id}`}
                                                        >
                                                          <X className="h-3 w-3" />
                                                        </button>
                                                      </div>
                                                    ))}
                                                  </div>
                                                </div>
                                              )}

                                              {itemProgress?.agentAnalysis && (
                                                <Card className="mt-3 p-3 bg-purple-50 border-purple-200">
                                                  <div className="flex items-start gap-2">
                                                    <Sparkles className="h-4 w-4 text-purple-600 mt-0.5" />
                                                    <div>
                                                      <p className="text-xs font-medium text-purple-700">Análise do Agent</p>
                                                      <p className="text-sm text-slate-700 mt-1 whitespace-pre-wrap">{itemProgress.agentAnalysis}</p>
                                                    </div>
                                                  </div>
                                                </Card>
                                              )}
                                            </div>
                                          </div>
                                          <div className="flex items-center gap-2">
                                            <label className="cursor-pointer">
                                              <input
                                                type="file"
                                                className="hidden"
                                                accept=".pdf,.xls,.xlsx,.csv,.json,.xml,.png,.jpg,.jpeg,.gif,.webp"
                                                onChange={(e) => {
                                                  const file = e.target.files?.[0];
                                                  if (file) {
                                                    uploadAttachment(item.id, file);
                                                    e.target.value = "";
                                                  }
                                                }}
                                                data-testid={`input-upload-${item.id}`}
                                              />
                                              <Button 
                                                size="sm" 
                                                variant="outline"
                                                asChild
                                                disabled={uploadingItem === item.id}
                                              >
                                                <span data-testid={`button-upload-${item.id}`}>
                                                  {uploadingItem === item.id ? (
                                                    <Loader2 className="h-4 w-4 animate-spin" />
                                                  ) : (
                                                    <Upload className="h-4 w-4" />
                                                  )}
                                                </span>
                                              </Button>
                                            </label>
                                            <Dialog>
                                              <DialogTrigger asChild>
                                                <Button 
                                                  size="sm" 
                                                  variant="outline"
                                                  onClick={() => {
                                                    setSelectedChecklistItem(item);
                                                    setChecklistItemNotes(itemProgress?.notes || "");
                                                    loadAttachments(item.id);
                                                  }}
                                                  data-testid={`button-agent-assist-${item.id}`}
                                                >
                                                  <Sparkles className="h-4 w-4 mr-1" /> Agent
                                                </Button>
                                              </DialogTrigger>
                                              <DialogContent className="max-w-2xl">
                                                <DialogHeader>
                                                  <DialogTitle>{item.code} - {item.title}</DialogTitle>
                                                  <DialogDescription>Solicite assistência do Agent para este item</DialogDescription>
                                                </DialogHeader>
                                                <div className="space-y-4">
                                                  <div>
                                                    <Label>Informações Coletadas</Label>
                                                    <Textarea
                                                      value={checklistItemNotes}
                                                      onChange={(e) => setChecklistItemNotes(e.target.value)}
                                                      placeholder="Descreva as informações que você coletou ou tem dúvidas..."
                                                      rows={4}
                                                      data-testid={`textarea-agent-notes-${item.id}`}
                                                    />
                                                  </div>
                                                  {itemProgress?.agentAnalysis && (
                                                    <Card className="p-3 bg-purple-50 border-purple-200">
                                                      <div className="flex items-start gap-2">
                                                        <Sparkles className="h-4 w-4 text-purple-600 mt-0.5" />
                                                        <div>
                                                          <p className="text-xs font-medium text-purple-700">Última Análise</p>
                                                          <p className="text-sm text-slate-700 mt-1 whitespace-pre-wrap">{itemProgress.agentAnalysis}</p>
                                                        </div>
                                                      </div>
                                                    </Card>
                                                  )}
                                                </div>
                                                <DialogFooter>
                                                  <Button
                                                    onClick={() => {
                                                      if (selectedChecklistItem) {
                                                        requestAgentAssist(selectedChecklistItem);
                                                      }
                                                    }}
                                                    disabled={checklistAgentLoading}
                                                    data-testid={`button-request-analysis-${item.id}`}
                                                  >
                                                    {checklistAgentLoading ? (
                                                      <>
                                                        <Loader2 className="h-4 w-4 mr-2 animate-spin" /> Analisando...
                                                      </>
                                                    ) : (
                                                      <>
                                                        <Sparkles className="h-4 w-4 mr-2" /> Solicitar Análise
                                                      </>
                                                    )}
                                                  </Button>
                                                </DialogFooter>
                                              </DialogContent>
                                            </Dialog>
                                          </div>
                                        </div>
                                      </div>
                                    );
                                  })}
                                </div>
                              )}
                            </Card>
                          );
                        })}
                      </div>
                    </div>
                  </TabsContent>

                  <TabsContent value="financials" className="mt-0">
                    <Card className="p-4">
                      <div className="flex items-center justify-between mb-4">
                        <h3 className="font-medium">Dados Financeiros por Ano</h3>
                        <div className="flex gap-2">
                          <Button size="sm" variant="outline" onClick={() => setImportOpen(true)} data-testid="button-import-data">
                            <Upload className="h-4 w-4 mr-2" /> Importar
                          </Button>
                          <Button size="sm" onClick={() => setNewInputOpen(true)} data-testid="button-add-input">
                            <Plus className="h-4 w-4 mr-2" /> Adicionar Ano
                          </Button>
                        </div>
                      </div>
                      {inputs.length > 0 ? (
                        <div className="overflow-x-auto">
                          <table className="w-full text-sm">
                            <thead>
                              <tr className="border-b">
                                <th className="text-left py-2 px-3">Ano</th>
                                <th className="text-right py-2 px-3">Receita</th>
                                <th className="text-right py-2 px-3">EBITDA</th>
                                <th className="text-right py-2 px-3">Lucro Líquido</th>
                                <th className="text-right py-2 px-3">Ativos</th>
                                <th className="text-right py-2 px-3">PL</th>
                                <th className="text-center py-2 px-3">Tipo</th>
                              </tr>
                            </thead>
                            <tbody>
                              {inputs.map((input) => (
                                <tr key={input.id} className="border-b">
                                  <td className="py-2 px-3 font-medium">{input.year}</td>
                                  <td className="text-right py-2 px-3">{formatCurrency(input.revenue)}</td>
                                  <td className="text-right py-2 px-3">{formatCurrency(input.ebitda)}</td>
                                  <td className="text-right py-2 px-3">{formatCurrency(input.netIncome)}</td>
                                  <td className="text-right py-2 px-3">{formatCurrency(input.totalAssets)}</td>
                                  <td className="text-right py-2 px-3">{formatCurrency(input.totalEquity)}</td>
                                  <td className="text-center py-2 px-3">
                                    <Badge variant="secondary" className={input.isProjection ? "bg-blue-100 text-blue-600" : "bg-green-100 text-green-600"}>
                                      {input.isProjection ? "Projeção" : "Realizado"}
                                    </Badge>
                                  </td>
                                </tr>
                              ))}
                            </tbody>
                          </table>
                        </div>
                      ) : (
                        <div className="text-center py-8 text-slate-500">
                          <DollarSign className="h-8 w-8 mx-auto mb-2 opacity-50" />
                          <p>Nenhum dado financeiro cadastrado</p>
                          <p className="text-xs">Adicione os dados históricos e projetados da empresa</p>
                        </div>
                      )}
                    </Card>
                  </TabsContent>

                  <TabsContent value="calculations" className="mt-0">
                    <Card className="p-4">
                      <div className="flex items-center justify-between mb-4">
                        <h3 className="font-medium">Metodologias de Valuation</h3>
                        <Button size="sm" data-testid="button-add-calculation">
                          <Calculator className="h-4 w-4 mr-2" /> Nova Metodologia
                        </Button>
                      </div>
                      {calculations.length > 0 ? (
                        <div className="space-y-3">
                          {calculations.map((calc) => (
                            <Card key={calc.id} className="p-4 border">
                              <div className="flex items-center justify-between">
                                <div>
                                  <h4 className="font-medium">{calc.method}</h4>
                                  <p className="text-sm text-slate-500">
                                    Peso: {calc.weight ? `${parseFloat(calc.weight) * 100}%` : "-"}
                                  </p>
                                </div>
                                <div className="text-right">
                                  <p className="text-sm text-slate-500">Enterprise Value</p>
                                  <p className="text-lg font-semibold text-emerald-600">
                                    {formatCurrency(calc.enterpriseValue)}
                                  </p>
                                </div>
                              </div>
                            </Card>
                          ))}
                        </div>
                      ) : (
                        <div className="text-center py-8 text-slate-500">
                          <Calculator className="h-8 w-8 mx-auto mb-2 opacity-50" />
                          <p>Nenhum cálculo realizado</p>
                          <p className="text-xs">Execute metodologias como DCF, Múltiplos ou Valor Patrimonial</p>
                        </div>
                      )}
                    </Card>
                  </TabsContent>

                  <TabsContent value="captable" className="mt-0">
                    <Card className="p-4">
                      <div className="flex items-center justify-between mb-4">
                        <h3 className="font-medium">Cap Table - Estrutura Societária</h3>
                        <Button size="sm" data-testid="button-add-shareholder">
                          <Users className="h-4 w-4 mr-2" /> Adicionar Sócio
                        </Button>
                      </div>
                      <div className="text-center py-8 text-slate-500">
                        <PieChart className="h-8 w-8 mx-auto mb-2 opacity-50" />
                        <p>Nenhum sócio cadastrado</p>
                        <p className="text-xs">Adicione a estrutura societária da empresa</p>
                      </div>
                    </Card>
                  </TabsContent>

                  <TabsContent value="documents" className="mt-0">
                    <Card className="p-4">
                      <div className="flex items-center justify-between mb-4">
                        <h3 className="font-medium">Data Room - Documentos</h3>
                        <Button size="sm" data-testid="button-upload-document">
                          <Plus className="h-4 w-4 mr-2" /> Upload
                        </Button>
                      </div>
                      <div className="text-center py-8 text-slate-500">
                        <FileText className="h-8 w-8 mx-auto mb-2 opacity-50" />
                        <p>Nenhum documento enviado</p>
                        <p className="text-xs">Envie documentos para o data room virtual</p>
                      </div>
                    </Card>
                  </TabsContent>

                  <TabsContent value="sector" className="mt-0">
                    <SectorAnalysisTab projectId={selectedProject.id} sector={selectedProject.sector} />
                  </TabsContent>

                                  </div>
              </Tabs>
            </>
          ) : (
            <div className="flex-1 flex items-center justify-center">
              <div className="text-center">
                <TrendingUp className="h-16 w-16 mx-auto mb-4 text-slate-300" />
                <h2 className="text-xl font-medium text-slate-600">Selecione um Projeto</h2>
                <p className="text-slate-500 mt-2">Escolha um projeto na lista ou crie um novo</p>
                <Button className="mt-4" onClick={() => setNewProjectOpen(true)} data-testid="button-create-first-project">
                  <Plus className="h-4 w-4 mr-2" /> Criar Primeiro Projeto
                </Button>
              </div>
            </div>
          )}
        </main>

        {/* Agent Panel */}
        {showAgentPanel && selectedProject && (
          <aside className="w-80 border-l bg-white flex flex-col">
            <div className="p-3 border-b flex items-center justify-between">
              <h3 className="font-medium flex items-center gap-2">
                <Bot className="h-4 w-4 text-primary" />
                Assistente IA
              </h3>
              <Button variant="ghost" size="sm" onClick={() => setShowAgentPanel(false)}>×</Button>
            </div>
            <ScrollArea className="flex-1 p-3">
              {agentMessages.length === 0 ? (
                <div className="text-center py-8 text-slate-400">
                  <Bot className="h-10 w-10 mx-auto mb-2" />
                  <p className="text-sm">Pergunte sobre valuation, metodologias, múltiplos de mercado...</p>
                </div>
              ) : (
                <div className="space-y-3">
                  {agentMessages.map((msg, i) => (
                    <div key={i} className={`p-2 rounded-lg text-sm ${msg.role === "user" ? "bg-primary text-white ml-4" : "bg-slate-100 mr-4"}`}>
                      {msg.content}
                    </div>
                  ))}
                  {agentLoading && (
                    <div className="flex items-center gap-2 text-slate-400 text-sm">
                      <Loader2 className="h-4 w-4 animate-spin" />
                      Pensando...
                    </div>
                  )}
                </div>
              )}
            </ScrollArea>
            <div className="p-3 border-t">
              <div className="flex gap-2">
                <Input
                  placeholder="Pergunte ao assistente..."
                  value={agentInput}
                  onChange={(e) => setAgentInput(e.target.value)}
                  onKeyDown={(e) => e.key === "Enter" && sendAgentMessage()}
                  disabled={agentLoading}
                />
                <Button size="sm" onClick={sendAgentMessage} disabled={agentLoading || !agentInput.trim()}>
                  <Send className="h-4 w-4" />
                </Button>
              </div>
            </div>
          </aside>
        )}

        {/* Floating Agent Button */}
        {selectedProject && !showAgentPanel && (
          <Button
            className="fixed bottom-6 right-6 h-12 w-12 rounded-full shadow-lg"
            onClick={() => setShowAgentPanel(true)}
            data-testid="button-open-agent"
          >
            <MessageSquare className="h-5 w-5" />
          </Button>
        )}
      </div>

      {/* Dialog para Adicionar Ano */}
      <Dialog open={newInputOpen} onOpenChange={setNewInputOpen}>
        <DialogContent className="max-w-2xl">
          <DialogHeader>
            <DialogTitle>Adicionar Dados Financeiros</DialogTitle>
            <DialogDescription>Informe os dados contábeis do ano selecionado</DialogDescription>
          </DialogHeader>
          <div className="grid grid-cols-3 gap-4">
            <div>
              <Label>Ano</Label>
              <Input
                type="number"
                value={newInput.year}
                onChange={(e) => setNewInput({ ...newInput, year: parseInt(e.target.value) || new Date().getFullYear() })}
              />
            </div>
            <div>
              <Label>Tipo</Label>
              <Select value={newInput.isProjection.toString()} onValueChange={(v) => setNewInput({ ...newInput, isProjection: parseInt(v) })}>
                <SelectTrigger>
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="0">Realizado</SelectItem>
                  <SelectItem value="1">Projeção</SelectItem>
                </SelectContent>
              </Select>
            </div>
            <div>
              <Label>Receita</Label>
              <Input
                type="number"
                placeholder="0,00"
                value={newInput.revenue}
                onChange={(e) => setNewInput({ ...newInput, revenue: e.target.value })}
              />
            </div>
            <div>
              <Label>Lucro Bruto</Label>
              <Input
                type="number"
                placeholder="0,00"
                value={newInput.grossProfit}
                onChange={(e) => setNewInput({ ...newInput, grossProfit: e.target.value })}
              />
            </div>
            <div>
              <Label>EBITDA</Label>
              <Input
                type="number"
                placeholder="0,00"
                value={newInput.ebitda}
                onChange={(e) => setNewInput({ ...newInput, ebitda: e.target.value })}
              />
            </div>
            <div>
              <Label>EBIT</Label>
              <Input
                type="number"
                placeholder="0,00"
                value={newInput.ebit}
                onChange={(e) => setNewInput({ ...newInput, ebit: e.target.value })}
              />
            </div>
            <div>
              <Label>Lucro Líquido</Label>
              <Input
                type="number"
                placeholder="0,00"
                value={newInput.netIncome}
                onChange={(e) => setNewInput({ ...newInput, netIncome: e.target.value })}
              />
            </div>
            <div>
              <Label>Total Ativos</Label>
              <Input
                type="number"
                placeholder="0,00"
                value={newInput.totalAssets}
                onChange={(e) => setNewInput({ ...newInput, totalAssets: e.target.value })}
              />
            </div>
            <div>
              <Label>Total Passivos</Label>
              <Input
                type="number"
                placeholder="0,00"
                value={newInput.totalLiabilities}
                onChange={(e) => setNewInput({ ...newInput, totalLiabilities: e.target.value })}
              />
            </div>
            <div>
              <Label>Patrimônio Líquido</Label>
              <Input
                type="number"
                placeholder="0,00"
                value={newInput.totalEquity}
                onChange={(e) => setNewInput({ ...newInput, totalEquity: e.target.value })}
              />
            </div>
            <div>
              <Label>Caixa</Label>
              <Input
                type="number"
                placeholder="0,00"
                value={newInput.cash}
                onChange={(e) => setNewInput({ ...newInput, cash: e.target.value })}
              />
            </div>
            <div>
              <Label>Dívidas</Label>
              <Input
                type="number"
                placeholder="0,00"
                value={newInput.debt}
                onChange={(e) => setNewInput({ ...newInput, debt: e.target.value })}
              />
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setNewInputOpen(false)}>Cancelar</Button>
            <Button onClick={() => createInputMutation.mutate(newInput)} disabled={createInputMutation.isPending}>
              {createInputMutation.isPending && <Loader2 className="h-4 w-4 mr-2 animate-spin" />}
              Salvar
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Dialog de Importação de Dados Financeiros */}
      <Dialog open={importOpen} onOpenChange={(open) => {
        setImportOpen(open);
        if (!open) {
          setImportStep("upload");
          setImportFile(null);
          setImportedData([]);
          setImportAnalysis("");
        }
      }}>
        <DialogContent className="max-w-3xl max-h-[80vh] overflow-y-auto">
          <DialogHeader>
            <DialogTitle>Importar Dados Financeiros</DialogTitle>
            <DialogDescription>
              {importStep === "upload" && "Envie um arquivo Excel ou CSV com o Balanço e DRE da empresa"}
              {importStep === "analyzing" && "Analisando arquivo com inteligência artificial..."}
              {importStep === "review" && "Revise os dados extraídos antes de confirmar"}
            </DialogDescription>
          </DialogHeader>

          {importStep === "upload" && (
            <div className="space-y-4">
              <div className="border-2 border-dashed border-slate-300 rounded-lg p-8 text-center">
                <Upload className="h-12 w-12 mx-auto text-slate-400 mb-4" />
                <p className="text-sm text-slate-600 mb-4">
                  Arraste um arquivo ou clique para selecionar
                </p>
                <input
                  type="file"
                  accept=".xlsx,.xls,.csv"
                  onChange={handleFileUpload}
                  className="hidden"
                  id="file-upload"
                />
                <label htmlFor="file-upload">
                  <Button asChild variant="outline">
                    <span>Selecionar Arquivo</span>
                  </Button>
                </label>
                <p className="text-xs text-slate-500 mt-4">
                  Formatos aceitos: Excel (.xlsx, .xls) ou CSV
                </p>
              </div>
              <div className="bg-blue-50 border border-blue-200 rounded-md p-3">
                <p className="text-sm text-blue-800">
                  <strong>Dica:</strong> O arquivo deve conter colunas com Ano, Receita, EBITDA, Lucro Líquido, Ativos, Passivos e Patrimônio Líquido. O agente irá identificar automaticamente os campos.
                </p>
              </div>
            </div>
          )}

          {importStep === "analyzing" && (
            <div className="py-12 text-center">
              <Loader2 className="h-12 w-12 mx-auto animate-spin text-emerald-600 mb-4" />
              <p className="text-lg font-medium">Analisando arquivo...</p>
              <p className="text-sm text-slate-500">O Arcádia Agent está extraindo e interpretando os dados</p>
            </div>
          )}

          {importStep === "review" && (
            <div className="space-y-4">
              {importAnalysis && (
                <div className="bg-emerald-50 border border-emerald-200 rounded-md p-4">
                  <div className="flex items-start gap-2">
                    <Bot className="h-5 w-5 text-emerald-600 mt-0.5" />
                    <div>
                      <p className="text-sm font-medium text-emerald-800">Análise do Agente:</p>
                      <p className="text-sm text-emerald-700 whitespace-pre-wrap">{importAnalysis}</p>
                    </div>
                  </div>
                </div>
              )}

              {importedData.length > 0 && (
                <div className="border rounded-md overflow-hidden">
                  <table className="w-full text-sm">
                    <thead className="bg-slate-100">
                      <tr>
                        <th className="px-3 py-2 text-left">Ano</th>
                        <th className="px-3 py-2 text-right">Receita</th>
                        <th className="px-3 py-2 text-right">EBITDA</th>
                        <th className="px-3 py-2 text-right">Lucro Líquido</th>
                        <th className="px-3 py-2 text-right">Ativos</th>
                        <th className="px-3 py-2 text-right">PL</th>
                      </tr>
                    </thead>
                    <tbody>
                      {importedData.map((row, idx) => (
                        <tr key={idx} className="border-t">
                          <td className="px-3 py-2">{row.year}</td>
                          <td className="px-3 py-2 text-right">{formatCurrency(row.revenue)}</td>
                          <td className="px-3 py-2 text-right">{formatCurrency(row.ebitda)}</td>
                          <td className="px-3 py-2 text-right">{formatCurrency(row.netIncome)}</td>
                          <td className="px-3 py-2 text-right">{formatCurrency(row.totalAssets)}</td>
                          <td className="px-3 py-2 text-right">{formatCurrency(row.totalEquity)}</td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              )}

              {importedData.length === 0 && (
                <div className="text-center py-8 text-slate-500">
                  <p>Nenhum dado foi extraído do arquivo.</p>
                  <p className="text-sm">Verifique se o formato está correto.</p>
                </div>
              )}
            </div>
          )}

          <DialogFooter>
            <Button variant="outline" onClick={() => setImportOpen(false)}>Cancelar</Button>
            {importStep === "review" && importedData.length > 0 && (
              <Button onClick={saveImportedData}>
                Importar {importedData.length} anos
              </Button>
            )}
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </BrowserFrame>
  );
}
