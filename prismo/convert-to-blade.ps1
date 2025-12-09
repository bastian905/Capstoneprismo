# Convert FE HTML files to Laravel Blade templates
$ErrorActionPreference = "Stop"

$processed = 0
$fe = "c:\Users\Pongo\Utama\capstoneprismo\fe"
$views = "c:\Users\Pongo\Utama\capstoneprismo\prismo\resources\views"
$publicCss = "c:\Users\Pongo\Utama\capstoneprismo\prismo\public\css"
$publicJs = "c:\Users\Pongo\Utama\capstoneprismo\prismo\public\js"
$publicImages = "c:\Users\Pongo\Utama\capstoneprismo\prismo\public\images"

Write-Host "=== Starting FE to Blade Conversion ===" -ForegroundColor Cyan
Write-Host "Source: $fe" -ForegroundColor Gray
Write-Host "Target: $views" -ForegroundColor Gray
Write-Host ""

# Create public directories if they don't exist
@($publicCss, $publicJs, $publicImages) | ForEach-Object {
    if(!(Test-Path $_)) {
        New-Item -ItemType Directory -Force -Path $_ | Out-Null
        Write-Host "Created: $_" -ForegroundColor Yellow
    }
}

# Function to convert HTML to Blade syntax
function Convert-ToBlade {
    param([string]$html)
    
    # Convert CSS links
    $html = $html -creplace 'href="(?!http)([^"]*\.css)"', 'href="{{ asset(''css/$1'') }}"'
    $html = $html -creplace "href='(?!http)([^']*\.css)'", "href=`"{{ asset('css/`$1') }}`""
    
    # Convert JS scripts
    $html = $html -creplace 'src="(?!http)([^"]*\.js)"', 'src="{{ asset(''js/$1'') }}"'
    $html = $html -creplace "src='(?!http)([^']*\.js)'", "src=`"{{ asset('js/`$1') }}`""
    
    # Convert image sources
    $html = $html -creplace 'src="/fe/images/([^"]+)"', 'src="{{ asset(''images/$1'') }}"'
    $html = $html -creplace "src='/fe/images/([^']+)'", "src=`"{{ asset('images/`$1') }}`""
    $html = $html -creplace 'src="../images/([^"]+)"', 'src="{{ asset(''images/$1'') }}"'
    $html = $html -creplace 'src="images/([^"]+)"', 'src="{{ asset(''images/$1'') }}"'
    
    # Convert HTML page links to Laravel routes
    $html = $html -creplace 'href="/fe/([^"]+)\.html"', 'href="{{ url(''/$1'') }}"'
    $html = $html -creplace "href='/fe/([^']+)\.html'", "href=`"{{ url('/`$1') }}`""
    $html = $html -creplace 'href="([^"]+)\.html"', 'href="{{ url(''/$1'') }}"'
    $html = $html -creplace "href='([^']+)\.html'", "href=`"{{ url('/`$1') }}`""
    
    # Convert JavaScript redirects
    $html = $html -creplace "window\.location\.href\s*=\s*'/fe/([^']+)\.html'", "window.location.href='{{ url('/`$1') }}'"
    $html = $html -creplace 'window\.location\.href\s*=\s*"/fe/([^"]+)\.html"', 'window.location.href="{{ url(''/$1'') }}"'
    $html = $html -creplace "location\.href\s*=\s*'/fe/([^']+)\.html'", "location.href='{{ url('/`$1') }}'"
    $html = $html -creplace 'location\.href\s*=\s*"/fe/([^"]+)\.html"', 'location.href="{{ url(''/$1'') }}"'
    
    return $html
}

# Get all HTML files
$htmlFiles = Get-ChildItem -Path $fe -Filter "*.html" -Recurse

if($htmlFiles.Count -eq 0) {
    Write-Host "No HTML files found in $fe" -ForegroundColor Red
    exit 1
}

Write-Host "Found $($htmlFiles.Count) HTML files to convert`n" -ForegroundColor Cyan

# Convert each HTML file to Blade
foreach($file in $htmlFiles) {
    try {
        # Read file content
        $content = Get-Content -Path $file.FullName -Raw -Encoding UTF8
        
        # Convert to Blade syntax
        $bladeContent = Convert-ToBlade $content
        
        # Determine target directory structure - use Substring instead of Replace
        $feLength = $fe.Length
        $fullDir = $file.DirectoryName
        
        if($fullDir.Length -gt $feLength) {
            $relativePath = $fullDir.Substring($feLength).TrimStart('\').TrimStart('/')
        } else {
            $relativePath = ""
        }
        
        if($relativePath -eq "") {
            $targetDir = $views
        } else {
            $targetDir = Join-Path -Path $views -ChildPath $relativePath
        }
        
        # Create target directory if needed
        if(!(Test-Path $targetDir)) {
            New-Item -ItemType Directory -Force -Path $targetDir | Out-Null
        }
        
        # Create blade file
        $bladeName = $file.BaseName + ".blade.php"
        $targetFile = Join-Path -Path $targetDir -ChildPath $bladeName
        
        # Write blade file
        Set-Content -Path $targetFile -Value $bladeContent -Encoding UTF8
        
        $processed++
        Write-Host "Converted: $($file.Name) -> $bladeName" -ForegroundColor Green
        
    } catch {
        Write-Host "Error converting $($file.Name): $_" -ForegroundColor Red
    }
}

# Copy CSS files
Write-Host "`nCopying CSS files..." -ForegroundColor Cyan
$cssFiles = Get-ChildItem -Path $fe -Filter "*.css" -Recurse
foreach($css in $cssFiles) {
    $targetCss = Join-Path $publicCss $css.Name
    Copy-Item $css.FullName -Destination $targetCss -Force
    Write-Host "Copied: $($css.Name)" -ForegroundColor Green
}

# Copy JS files
Write-Host "`nCopying JS files..." -ForegroundColor Cyan
$jsFiles = Get-ChildItem -Path $fe -Filter "*.js" -Recurse
foreach($js in $jsFiles) {
    $targetJs = Join-Path $publicJs $js.Name
    Copy-Item $js.FullName -Destination $targetJs -Force
    Write-Host "Copied: $($js.Name)" -ForegroundColor Green
}

# Copy image files
Write-Host "`nCopying image files..." -ForegroundColor Cyan
$imageDir = Join-Path $fe "images"
if(Test-Path $imageDir) {
    Copy-Item -Path "$imageDir\*" -Destination $publicImages -Recurse -Force
    $imageCount = (Get-ChildItem -Path $imageDir -Recurse -File).Count
    Write-Host "Copied $imageCount image files" -ForegroundColor Green
}

# Summary
Write-Host "`n=== CONVERSION SUMMARY ===" -ForegroundColor Yellow
Write-Host "HTML files converted to Blade: $processed" -ForegroundColor Cyan
Write-Host "CSS files copied: $($cssFiles.Count)" -ForegroundColor Cyan
Write-Host "JS files copied: $($jsFiles.Count)" -ForegroundColor Cyan
Write-Host "`nBlade files location: $views" -ForegroundColor Gray
Write-Host "Public assets location: c:\Users\Pongo\Utama\capstoneprismo\prismo\public\" -ForegroundColor Gray
Write-Host "`nConversion completed successfully!" -ForegroundColor Green
