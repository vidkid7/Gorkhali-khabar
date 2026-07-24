$ErrorActionPreference = "Stop"
$services = docker compose config --services
$required = @("frontend", "backend", "worker", "scheduler", "web", "postgres", "redis", "mailpit")
$missing = $required | Where-Object { $_ -notin $services }
if ($missing) { throw "Missing services: $($missing -join ', ')" }
