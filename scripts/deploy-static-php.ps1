$ErrorActionPreference = "Stop"
Set-StrictMode -Version Latest

$repoRoot = Split-Path -Parent $PSScriptRoot
$distRoot = Join-Path $repoRoot "static-site\dist"
$rulesPath = Join-Path $repoRoot "static-site\deploy\.htaccess"
$archivePath = Join-Path $repoRoot "gorkhali-static-release.tgz"
$installerPath = Join-Path $repoRoot "scripts\cpanel-install-static.sh"
$sshKey = "C:\Users\A C E R\.ssh\cpanel_gorkhali"
$remote = "gorkhal1@alpha.mysecurecloudserver.com"
$remoteArchive = "/home1/gorkhal1/gorkhali-static-release.tgz"
$remoteInstaller = "/home1/gorkhal1/cpanel-install-static.sh"

if (-not (Test-Path -LiteralPath $sshKey -PathType Leaf)) {
  throw "The cPanel SSH key was not found."
}

Push-Location $repoRoot
try {
  & npm.cmd run static:test
  if ($LASTEXITCODE -ne 0) { throw "Static tests failed." }

  & npm.cmd run static:build
  if ($LASTEXITCODE -ne 0) { throw "Static build failed." }

  Copy-Item -LiteralPath $rulesPath -Destination (Join-Path $distRoot ".htaccess") -Force
  if (Test-Path -LiteralPath $archivePath) {
    Remove-Item -LiteralPath $archivePath -Force
  }
  & tar.exe -czf $archivePath -C $distRoot .
  if ($LASTEXITCODE -ne 0) { throw "Release archive creation failed." }

  & scp.exe -i $sshKey -o BatchMode=yes -o StrictHostKeyChecking=accept-new $archivePath "${remote}:$remoteArchive"
  if ($LASTEXITCODE -ne 0) { throw "Release upload failed." }
  & scp.exe -i $sshKey -o BatchMode=yes -o StrictHostKeyChecking=accept-new $installerPath "${remote}:$remoteInstaller"
  if ($LASTEXITCODE -ne 0) { throw "Installer upload failed." }

  $remoteCommand = "sed -i 's/\r$//' '$remoteInstaller' && bash '$remoteInstaller' '$remoteArchive'"
  & ssh.exe -i $sshKey -o BatchMode=yes $remote $remoteCommand
  if ($LASTEXITCODE -ne 0) { throw "Remote installation failed." }
}
finally {
  Pop-Location
}
