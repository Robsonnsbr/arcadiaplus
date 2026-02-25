import { Switch, Route } from "wouter";
import { queryClient } from "./lib/queryClient";
import { QueryClientProvider } from "@tanstack/react-query";
import { Toaster } from "@/components/ui/toaster";
import { TooltipProvider } from "@/components/ui/tooltip";
import { AuthProvider } from "@/hooks/use-auth";
import { SoeMotorProvider } from "@/contexts/SoeMotorContext";
import { ProtectedRoute } from "@/lib/protected-route";
import { CommandPalette } from "@/components/CommandPalette";
import { KnowledgeCollectorInit } from "@/components/KnowledgeCollectorInit";
import NotFound from "@/pages/not-found";
import AuthPage from "@/pages/auth-page";
import Agent from "@/pages/Agent";
import Admin from "@/pages/Admin";
import Chat from "@/pages/Chat";
import WhatsApp from "@/pages/WhatsApp";
import Automations from "@/pages/Automations";
import BiWorkspace from "@/pages/BiWorkspace";
import ProcessCompass from "@/pages/ProcessCompass";
import WorkspacePage from "@/pages/WorkspacePage";
import AppViewer from "@/pages/AppViewer";
import Crm from "@/pages/Crm";
import Production from "@/pages/Production";
import Support from "@/pages/Support";
import Valuation from "@/pages/Valuation";
import Canvas from "@/pages/Canvas";
import IDE from "@/pages/IDE";
import Scientist from "@/pages/Scientist";
import Knowledge from "@/pages/Knowledge";
import CentralApis from "@/pages/CentralApis";
import ApiTesterPage from "@/pages/ApiTesterPage";
import ApiHub from "@/pages/ApiHub";
import Cockpit from "@/pages/Cockpit";
import Fisco from "@/pages/Fisco";
import People from "@/pages/People";
import Contabil from "@/pages/Contabil";
import SOE from "@/pages/SOE";
import Financeiro from "@/pages/Financeiro";
import Communities from "@/pages/Communities";
import ArcadiaNext from "@/pages/ArcadiaNext";
import QualityModule from "@/pages/QualityModule";
import CommercialEnv from "@/pages/CommercialEnv";
import FieldOperations from "@/pages/FieldOperations";
import TechnicalModule from "@/pages/TechnicalModule";
import SuppliersPortal from "@/pages/SuppliersPortal";
import NPSSurvey from "@/pages/NPSSurvey";
import EngineeringHub from "@/pages/EngineeringHub";
import DocTypeBuilder from "@/pages/DocTypeBuilder";
import PageBuilder from "@/pages/PageBuilder";
import DevelopmentModule from "@/pages/DevelopmentModule";
import ArcadiaRetail from "@/pages/ArcadiaRetail";
import Plus from "@/pages/Plus";
import SuperAdmin from "@/pages/SuperAdmin";
import Marketplace from "@/pages/Marketplace";
import LMS from "@/pages/LMS";
import AppCenter from "@/pages/AppCenter";
import XosCentral from "@/pages/XosCentral";
import XosCrm from "@/pages/XosCrm";
import XosInbox from "@/pages/XosInbox";
import XosTickets from "@/pages/XosTickets";
import Migration from "@/pages/Migration";
import DevCenter from "@/pages/DevCenter";
import XosCampaigns from "@/pages/XosCampaigns";
import XosAutomations from "@/pages/XosAutomations";
import XosSites from "@/pages/XosSites";
import XosGovernance from "@/pages/XosGovernance";
import XosPipeline from "@/pages/XosPipeline";


function Router() {
  return (
    <Switch>
      <ProtectedRoute path="/" component={Cockpit} />
      <ProtectedRoute path="/agent" component={Agent} />
      <ProtectedRoute path="/admin" component={Admin} />
      <ProtectedRoute path="/chat" component={Chat} />
      <ProtectedRoute path="/whatsapp" component={WhatsApp} />
      <ProtectedRoute path="/comunicacao" component={XosInbox} />
      <ProtectedRoute path="/automations" component={Automations} />
      <ProtectedRoute path="/insights" component={BiWorkspace} />
      <ProtectedRoute path="/compass" component={ProcessCompass} />
      <ProtectedRoute path="/crm" component={Crm} />
      <ProtectedRoute path="/production" component={Production} />
      <ProtectedRoute path="/support" component={Support} />
      <ProtectedRoute path="/valuation" component={Valuation} />
      <ProtectedRoute path="/canvas" component={Canvas} />
      <ProtectedRoute path="/ide" component={IDE} />
      <ProtectedRoute path="/scientist" component={Scientist} />
      <ProtectedRoute path="/knowledge" component={Knowledge} />
      <ProtectedRoute path="/central-apis" component={CentralApis} />
      <ProtectedRoute path="/api-tester" component={ApiTesterPage} />
      <ProtectedRoute path="/api-hub" component={ApiHub} />
      <ProtectedRoute path="/fisco" component={Fisco} />
      <ProtectedRoute path="/people" component={People} />
      <ProtectedRoute path="/contabil" component={Contabil} />
      <ProtectedRoute path="/soe" component={SOE} />
      <ProtectedRoute path="/erp" component={SOE} />
      <ProtectedRoute path="/financeiro" component={Financeiro} />
      <ProtectedRoute path="/communities" component={Communities} />
      <ProtectedRoute path="/quality" component={QualityModule} />
      <ProtectedRoute path="/commercial-env" component={CommercialEnv} />
      <ProtectedRoute path="/field-ops" component={FieldOperations} />
      <ProtectedRoute path="/technical" component={TechnicalModule} />
      <ProtectedRoute path="/suppliers" component={SuppliersPortal} />
      <ProtectedRoute path="/nps" component={NPSSurvey} />
      <ProtectedRoute path="/engineering" component={EngineeringHub} />
      <ProtectedRoute path="/development" component={DevelopmentModule} />
      <ProtectedRoute path="/retail" component={ArcadiaRetail} />
      <ProtectedRoute path="/plus" component={Plus} />
      <ProtectedRoute path="/super-admin" component={SuperAdmin} />
      <ProtectedRoute path="/marketplace" component={Marketplace} />
      <ProtectedRoute path="/lms" component={LMS} />
      <ProtectedRoute path="/apps" component={AppCenter} />
      <ProtectedRoute path="/xos" component={XosCentral} />
      <ProtectedRoute path="/xos/crm" component={XosCrm} />
      <ProtectedRoute path="/xos/inbox" component={XosInbox} />
      <ProtectedRoute path="/xos/tickets" component={XosTickets} />
      <ProtectedRoute path="/xos/campaigns" component={XosCampaigns} />
      <ProtectedRoute path="/xos/automations" component={XosAutomations} />
      <ProtectedRoute path="/xos/sites" component={XosSites} />
      <ProtectedRoute path="/xos/governance" component={XosGovernance} />
      <ProtectedRoute path="/xos/pipeline" component={XosPipeline} />

      <ProtectedRoute path="/doctype-builder" component={DocTypeBuilder} />
      <ProtectedRoute path="/page-builder" component={PageBuilder} />
      <ProtectedRoute path="/migration" component={Migration} />
      <ProtectedRoute path="/dev-center" component={DevCenter} />
      <ProtectedRoute path="/page/:id" component={WorkspacePage} />
      <ProtectedRoute path="/app/:id" component={AppViewer} />
      <Route path="/auth" component={AuthPage} />
      <Route component={NotFound} />
    </Switch>
  );
}

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <AuthProvider>
        <SoeMotorProvider>
          <TooltipProvider>
            <KnowledgeCollectorInit />
            <Toaster />
            <CommandPalette />
            <Router />
          </TooltipProvider>
        </SoeMotorProvider>
      </AuthProvider>
    </QueryClientProvider>
  );
}

export default App;
