import express, { type Request, Response, NextFunction } from "express";
import { registerRoutes } from "./routes";
import { serveStatic } from "./static";
import { registerAllTools } from "./autonomous/tools";
import { createServer } from "http";
import { spawn } from "child_process";
import path from "path";

interface ManagedService {
  name: string;
  scriptPath: string;
  port: number;
  process: ReturnType<typeof spawn> | null;
  status: "running" | "stopped" | "restarting";
  startedAt: Date | null;
  restartCount: number;
  logs: string[];
}

const managedServices: Map<string, ManagedService> = new Map();
const MAX_LOG_LINES = 200;

function startPythonService(name: string, scriptPath: string, port: number) {
  const existing = managedServices.get(name);

  const pythonProcess = spawn("python3", [scriptPath], {
    env: { ...process.env, [`${name.toUpperCase()}_PORT`]: String(port) },
    stdio: ["pipe", "pipe", "pipe"],
    cwd: process.cwd(),
  });

  const service: ManagedService = {
    name,
    scriptPath,
    port,
    process: pythonProcess,
    status: "running",
    startedAt: new Date(),
    restartCount: existing ? existing.restartCount : 0,
    logs: existing ? existing.logs : [],
  };

  const pushLog = (line: string) => {
    service.logs.push(`[${new Date().toISOString()}] ${line}`);
    if (service.logs.length > MAX_LOG_LINES) {
      service.logs = service.logs.slice(-MAX_LOG_LINES);
    }
  };

  pythonProcess.stdout?.on("data", (data) => {
    const msg = data.toString().trim();
    console.log(`[${name}] ${msg}`);
    pushLog(`[stdout] ${msg}`);
  });

  pythonProcess.stderr?.on("data", (data) => {
    const msg = data.toString().trim();
    console.error(`[${name}] ${msg}`);
    pushLog(`[stderr] ${msg}`);
  });

  pythonProcess.on("close", (code) => {
    console.log(`[${name}] Process exited with code ${code}`);
    pushLog(`Process exited with code ${code}`);
    if (service.status === "stopped" || service.status === "restarting") {
      service.process = null;
      return;
    }
    service.process = null;
    if (code !== 0) {
      service.status = "restarting";
      service.restartCount++;
      console.log(`[${name}] Restarting in 5 seconds...`);
      setTimeout(() => startPythonService(name, scriptPath, port), 5000);
    } else {
      service.status = "stopped";
    }
  });

  managedServices.set(name, service);
  return pythonProcess;
}

function restartManagedService(name: string): boolean {
  const service = managedServices.get(name);
  if (!service) return false;

  if (service.process) {
    service.status = "restarting";
    const proc = service.process;
    service.process = null;
    proc.kill("SIGTERM");
    setTimeout(() => {
      try { proc.kill("SIGKILL"); } catch {}
      startPythonService(service.name, service.scriptPath, service.port);
    }, 2000);
  } else {
    startPythonService(service.name, service.scriptPath, service.port);
  }
  return true;
}

function stopManagedService(name: string): boolean {
  const service = managedServices.get(name);
  if (!service || !service.process) return false;
  service.status = "stopped";
  const proc = service.process;
  service.process = null;
  proc.kill("SIGTERM");
  setTimeout(() => {
    try { proc.kill("SIGKILL"); } catch {}
  }, 3000);
  return true;
}

function getManagedServiceInfo(name: string) {
  const service = managedServices.get(name);
  if (!service) return null;
  return {
    name: service.name,
    port: service.port,
    status: service.status,
    startedAt: service.startedAt?.toISOString() || null,
    restartCount: service.restartCount,
    uptime: service.startedAt ? Math.round((Date.now() - service.startedAt.getTime()) / 1000) : 0,
    logLines: service.logs.length,
  };
}

function getManagedServiceLogs(name: string, lines = 50): string[] {
  const service = managedServices.get(name);
  if (!service) return [];
  return service.logs.slice(-lines);
}

function startNodeService(name: string, scriptPath: string, port: number) {
  const existing = managedServices.get(name);

  const service: ManagedService = {
    name,
    scriptPath,
    port,
    process: null,
    status: "running",
    startedAt: new Date(),
    restartCount: existing?.restartCount || 0,
    logs: existing?.logs || [],
  };

  const pushLog = (msg: string) => {
    service.logs.push(`[${new Date().toISOString()}] ${msg}`);
    if (service.logs.length > MAX_LOG_LINES) service.logs.splice(0, service.logs.length - MAX_LOG_LINES);
  };

  const nodeProcess = spawn("npx", ["tsx", scriptPath], {
    env: { ...process.env, PORT: port.toString() },
    stdio: ["pipe", "pipe", "pipe"],
  });

  service.process = nodeProcess;
  pushLog(`Node service started (PID: ${nodeProcess.pid})`);

  nodeProcess.stdout?.on("data", (data) => {
    const msg = data.toString().trim();
    if (msg) {
      console.log(`[${name}] ${msg}`);
      pushLog(msg);
    }
  });

  nodeProcess.stderr?.on("data", (data) => {
    const msg = data.toString().trim();
    if (msg) {
      console.error(`[${name}] ${msg}`);
      pushLog(`[stderr] ${msg}`);
    }
  });

  nodeProcess.on("close", (code) => {
    console.log(`[${name}] Process exited with code ${code}`);
    pushLog(`Process exited with code ${code}`);
    if (service.status === "stopped" || service.status === "restarting") {
      service.process = null;
      return;
    }
    service.process = null;
    if (code !== 0) {
      service.status = "restarting";
      service.restartCount++;
      console.log(`[${name}] Restarting in 5 seconds...`);
      setTimeout(() => startNodeService(name, scriptPath, port), 5000);
    } else {
      service.status = "stopped";
    }
  });

  managedServices.set(name, service);
  return nodeProcess;
}

export { managedServices, restartManagedService, stopManagedService, getManagedServiceInfo, getManagedServiceLogs, startNodeService };

startPythonService("contabil", path.join(process.cwd(), "server/python/contabil_service.py"), 8003);
startPythonService("bi", path.join(process.cwd(), "server/python/bi_engine.py"), 8004);
startPythonService("automation", path.join(process.cwd(), "server/python/automation_engine.py"), 8005);
startNodeService("communication", path.join(process.cwd(), "server/communication/engine.ts"), 8006);

function startShellService(name: string, scriptPath: string, port: number) {
  const existing = managedServices.get(name);

  const service: ManagedService = {
    name,
    scriptPath,
    port,
    process: null,
    status: "running",
    startedAt: new Date(),
    restartCount: existing?.restartCount || 0,
    logs: existing?.logs || [],
  };

  const pushLog = (msg: string) => {
    service.logs.push(`[${new Date().toISOString()}] ${msg}`);
    if (service.logs.length > MAX_LOG_LINES) service.logs.splice(0, service.logs.length - MAX_LOG_LINES);
  };

  const shellProcess = spawn("bash", [scriptPath], {
    env: { ...process.env },
    stdio: ["pipe", "pipe", "pipe"],
  });

  service.process = shellProcess;
  pushLog(`Shell service started (PID: ${shellProcess.pid})`);

  shellProcess.stdout?.on("data", (data) => {
    const msg = data.toString().trim();
    if (msg) {
      console.log(`[${name}] ${msg}`);
      pushLog(msg);
    }
  });

  shellProcess.stderr?.on("data", (data) => {
    const msg = data.toString().trim();
    if (msg) {
      console.error(`[${name}] ${msg}`);
      pushLog(`[stderr] ${msg}`);
    }
  });

  shellProcess.on("close", (code) => {
    console.log(`[${name}] Process exited with code ${code}`);
    pushLog(`Process exited with code ${code}`);
    if (service.status === "stopped" || service.status === "restarting") {
      service.process = null;
      return;
    }
    service.process = null;
    if (code !== 0) {
      service.status = "restarting";
      service.restartCount++;
      console.log(`[${name}] Restarting in 10 seconds...`);
      setTimeout(() => startShellService(name, scriptPath, port), 10000);
    } else {
      service.status = "stopped";
    }
  });

  managedServices.set(name, service);
  return shellProcess;
}

startShellService("metabase", path.join(process.cwd(), "metabase/start-metabase.sh"), 8088);

const app = express();
const httpServer = createServer(app);

declare module "http" {
  interface IncomingMessage {
    rawBody: unknown;
  }
}

app.use(
  express.json({
    limit: '10mb',
    verify: (req, _res, buf) => {
      req.rawBody = buf;
    },
  }),
);

app.use(express.urlencoded({ extended: false }));

export function log(message: string, source = "express") {
  const formattedTime = new Date().toLocaleTimeString("en-US", {
    hour: "numeric",
    minute: "2-digit",
    second: "2-digit",
    hour12: true,
  });

  console.log(`${formattedTime} [${source}] ${message}`);
}

app.use((req, res, next) => {
  const start = Date.now();
  const path = req.path;
  let capturedJsonResponse: Record<string, any> | undefined = undefined;

  const originalResJson = res.json;
  res.json = function (bodyJson, ...args) {
    capturedJsonResponse = bodyJson;
    return originalResJson.apply(res, [bodyJson, ...args]);
  };

  res.on("finish", () => {
    const duration = Date.now() - start;
    if (path.startsWith("/api")) {
      let logLine = `${req.method} ${path} ${res.statusCode} in ${duration}ms`;
      if (capturedJsonResponse) {
        logLine += ` :: ${JSON.stringify(capturedJsonResponse)}`;
      }

      log(logLine);
    }
  });

  next();
});

// Plus proxy is configured in server/plus/proxy.ts via setupPlusProxy
// It's registered after session middleware to enable SSO authentication

(async () => {
  await registerRoutes(httpServer, app);

  await registerAllTools();

  const { jobQueueService } = await import("./governance/jobQueue");
  const { pipelineOrchestrator } = await import("./blackboard/PipelineOrchestrator");
  pipelineOrchestrator.registerJobHandlers();
  jobQueueService.startProcessing();

  app.use((err: any, _req: Request, res: Response, _next: NextFunction) => {
    const status = err.status || err.statusCode || 500;
    const message = err.message || "Internal Server Error";

    res.status(status).json({ message });
    throw err;
  });

  // importantly only setup vite in development and after
  // setting up all the other routes so the catch-all route
  // doesn't interfere with the other routes
  if (process.env.NODE_ENV === "production") {
    serveStatic(app);
  } else {
    const { setupVite } = await import("./vite");
    await setupVite(httpServer, app);
  }

  // ALWAYS serve the app on the port specified in the environment variable PORT
  // Other ports are firewalled. Default to 5000 if not specified.
  // this serves both the API and the client.
  // It is the only port that is not firewalled.
  const port = parseInt(process.env.PORT || "5000", 10);
  httpServer.listen(
    {
      port,
      host: "0.0.0.0",
      reusePort: true,
    },
    () => {
      log(`serving on port ${port}`);
    },
  );
})();
