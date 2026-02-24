# Migracja Oaza dla Autyzmu: Laravel → ASP.NET Core

## Spis treści
1. [Dlaczego ASP.NET Core dla Skandynawii?](#dlaczego-aspnet-core)
2. [Architektura docelowa](#architektura-docelowa)
3. [Plan migracji krok po kroku](#plan-migracji)
4. [Stack technologiczny](#stack-technologiczny)
5. [Roadmapa rozwoju](#roadmapa-rozwoju)

---

## Dlaczego ASP.NET Core dla Skandynawii?

### Rynek skandynawski:
- 🇳🇴 **Norwegia**: 78% firm korzysta z .NET
- 🇸🇪 **Szwecja**: Microsoft ma silną pozycję (Spotify, Klarna używają .NET)
- 🇩🇰 **Dania**: Duże zapotrzebowanie na devów C#/.NET (€60-90k rocznie)
- 🇫🇮 **Finlandia**: Nokia, Microsoft - silny .NET ecosystem

### Zalety ASP.NET Core:
- ✅ **Performance**: 7x szybszy niż PHP Laravel (TechEmpower benchmarks)
- ✅ **Type Safety**: Strong typing = mniej bugów
- ✅ **Azure Integration**: Skandynawskie firmy używają Azure
- ✅ **Enterprise Ready**: ERP, CRM, Banking - wszystko na .NET
- ✅ **Wysokie stawki**: C# devs zarabiają 30-40% więcej niż PHP devs
- ✅ **Maintainability**: Kod łatwiejszy do utrzymania w dużych teamach

### Potencjał komercyjny:
1. **SaaS dla placówek**: System zarządzania placówkami dla autystycznych dzieci
2. **B2B dla szkół**: Platforma edukacyjna dla nauczycieli
3. **Wersja White-Label**: Sprzedaż licencji dla innych krajów EU
4. **Consulting**: Tworzenie podobnych rozwiązań dla klientów

---

## Architektura docelowa

### Clean Architecture + CQRS

```
src/
├── OazaDlaAutyzmu.Web/                 # ASP.NET Core MVC + API
│   ├── Controllers/
│   ├── Views/
│   ├── wwwroot/
│   └── Program.cs
│
├── OazaDlaAutyzmu.Application/         # Business Logic (CQRS)
│   ├── Commands/
│   │   ├── Facilities/
│   │   ├── Forums/
│   │   └── Articles/
│   ├── Queries/
│   │   ├── Facilities/
│   │   ├── Forums/
│   │   └── Articles/
│   ├── DTOs/
│   └── Interfaces/
│
├── OazaDlaAutyzmu.Domain/              # Domain Models
│   ├── Entities/
│   │   ├── Facility.cs
│   │   ├── User.cs
│   │   ├── Article.cs
│   │   └── ForumTopic.cs
│   ├── ValueObjects/
│   └── Interfaces/
│
├── OazaDlaAutyzmu.Infrastructure/      # Data Access + Services
│   ├── Data/
│   │   ├── ApplicationDbContext.cs
│   │   └── Migrations/
│   ├── Repositories/
│   ├── Services/
│   │   ├── EmailService.cs
│   │   └── StorageService.cs
│   └── Identity/
│
└── OazaDlaAutyzmu.Tests/
    ├── Unit/
    ├── Integration/
    └── E2E/
```

---

## Plan migracji krok po kroku

### FAZA 1: Setup projektu (1-2 dni)

#### Krok 1: Utworzenie struktury projektu
```bash
dotnet new sln -n OazaDlaAutyzmu
dotnet new mvc -n OazaDlaAutyzmu.Web
dotnet new classlib -n OazaDlaAutyzmu.Application
dotnet new classlib -n OazaDlaAutyzmu.Domain
dotnet new classlib -n OazaDlaAutyzmu.Infrastructure
dotnet new xunit -n OazaDlaAutyzmu.Tests

dotnet sln add **/*.csproj
```

#### Krok 2: Dodanie podstawowych pakietów
```xml
<!-- OazaDlaAutyzmu.Web -->
<PackageReference Include="Microsoft.AspNetCore.Identity.EntityFrameworkCore" Version="8.0.*" />
<PackageReference Include="Npgsql.EntityFrameworkCore.PostgreSQL" Version="8.0.*" />
<PackageReference Include="MediatR" Version="12.2.*" />
<PackageReference Include="FluentValidation.AspNetCore" Version="11.3.*" />
<PackageReference Include="Serilog.AspNetCore" Version="8.0.*" />

<!-- OazaDlaAutyzmu.Infrastructure -->
<PackageReference Include="Npgsql.EntityFrameworkCore.PostgreSQL" Version="8.0.*" />
<PackageReference Include="Microsoft.EntityFrameworkCore.Tools" Version="8.0.*" />
<PackageReference Include="Dapper" Version="2.1.*" />

<!-- OazaDlaAutyzmu.Application -->
<PackageReference Include="MediatR" Version="12.2.*" />
<PackageReference Include="FluentValidation" Version="11.9.*" />
<PackageReference Include="AutoMapper" Version="12.0.*" />
```

---

### FAZA 2: Migracja modeli danych (2-3 dni)

#### Krok 3: Domain Entities

**Domain/Entities/Facility.cs:**
```csharp
namespace OazaDlaAutyzmu.Domain.Entities;

public class Facility : BaseEntity
{
    public string Name { get; set; } = string.Empty;
    public string? Description { get; set; }
    public string Address { get; set; } = string.Empty;
    public string City { get; set; } = string.Empty;
    public string? PostalCode { get; set; }
    public string? PhoneNumber { get; set; }
    public string? Email { get; set; }
    public string? Website { get; set; }
    public FacilityType Type { get; set; }
    public decimal? Latitude { get; set; }
    public decimal? Longitude { get; set; }
    
    // Verification fields
    public string? Source { get; set; }
    public VerificationStatus VerificationStatus { get; set; } = VerificationStatus.Unverified;
    public int? VerifiedById { get; set; }
    public DateTime? VerifiedAt { get; set; }
    public string? VerificationNotes { get; set; }
    
    // Navigation properties
    public User? VerifiedBy { get; set; }
    public ICollection<Review> Reviews { get; set; } = new List<Review>();
    public ICollection<Visit> Visits { get; set; } = new List<Visit>();
}

public enum VerificationStatus
{
    Unverified,
    Verified,
    Certified,
    Flagged
}

public enum FacilityType
{
    Therapy,
    School,
    SupportCenter,
    Clinic,
    Other
}
```

**Domain/Entities/User.cs:**
```csharp
using Microsoft.AspNetCore.Identity;

namespace OazaDlaAutyzmu.Domain.Entities;

public class ApplicationUser : IdentityUser<int>
{
    public string? FirstName { get; set; }
    public string? LastName { get; set; }
    public UserRole Role { get; set; } = UserRole.User;
    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;
    public DateTime? SuspendedAt { get; set; }
    public string? SuspensionReason { get; set; }
    
    // Navigation properties
    public ICollection<ForumTopic> ForumTopics { get; set; } = new List<ForumTopic>();
    public ICollection<ForumPost> ForumPosts { get; set; } = new List<ForumPost>();
    public ICollection<Review> Reviews { get; set; } = new List<Review>();
    public ICollection<Article> Articles { get; set; } = new List<Article>();
}

public enum UserRole
{
    User,
    Moderator,
    Admin
}
```

#### Krok 4: DbContext

**Infrastructure/Data/ApplicationDbContext.cs:**
```csharp
using Microsoft.AspNetCore.Identity;
using Microsoft.AspNetCore.Identity.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore;
using OazaDlaAutyzmu.Domain.Entities;

namespace OazaDlaAutyzmu.Infrastructure.Data;

public class ApplicationDbContext : IdentityDbContext<ApplicationUser, IdentityRole<int>, int>
{
    public ApplicationDbContext(DbContextOptions<ApplicationDbContext> options)
        : base(options)
    {
    }

    public DbSet<Facility> Facilities { get; set; }
    public DbSet<Review> Reviews { get; set; }
    public DbSet<Article> Articles { get; set; }
    public DbSet<ArticleCategory> ArticleCategories { get; set; }
    public DbSet<ForumCategory> ForumCategories { get; set; }
    public DbSet<ForumTopic> ForumTopics { get; set; }
    public DbSet<ForumPost> ForumPosts { get; set; }
    public DbSet<Event> Events { get; set; }
    public DbSet<Visit> Visits { get; set; }
    public DbSet<Conversation> Conversations { get; set; }
    public DbSet<Message> Messages { get; set; }
    public DbSet<AuditLog> AuditLogs { get; set; }

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        base.OnModelCreating(modelBuilder);

        // Facility configuration
        modelBuilder.Entity<Facility>(entity =>
        {
            entity.HasKey(e => e.Id);
            entity.Property(e => e.Name).IsRequired().HasMaxLength(255);
            entity.Property(e => e.Address).IsRequired().HasMaxLength(500);
            entity.Property(e => e.City).IsRequired().HasMaxLength(100);
            entity.Property(e => e.Latitude).HasPrecision(10, 8);
            entity.Property(e => e.Longitude).HasPrecision(11, 8);
            
            entity.HasOne(e => e.VerifiedBy)
                .WithMany()
                .HasForeignKey(e => e.VerifiedById)
                .OnDelete(DeleteBehavior.SetNull);

            entity.HasIndex(e => e.City);
            entity.HasIndex(e => e.VerificationStatus);
        });

        // Review configuration
        modelBuilder.Entity<Review>(entity =>
        {
            entity.HasOne(e => e.User)
                .WithMany(u => u.Reviews)
                .HasForeignKey(e => e.UserId)
                .OnDelete(DeleteBehavior.Cascade);

            entity.HasOne(e => e.Facility)
                .WithMany(f => f.Reviews)
                .HasForeignKey(e => e.FacilityId)
                .OnDelete(DeleteBehavior.Cascade);
        });

        // Seed data
        modelBuilder.Entity<ArticleCategory>().HasData(
            new ArticleCategory { Id = 1, Name = "Podstawy", Slug = "podstawy" },
            new ArticleCategory { Id = 2, Name = "Diagnoza", Slug = "diagnoza" },
            new ArticleCategory { Id = 3, Name = "Terapie", Slug = "terapie" }
        );
    }
}
```

---

### FAZA 3: CQRS + MediatR (3-4 dni)

#### Krok 5: Commands & Queries

**Application/Commands/Facilities/CreateFacilityCommand.cs:**
```csharp
using MediatR;

namespace OazaDlaAutyzmu.Application.Commands.Facilities;

public record CreateFacilityCommand : IRequest<int>
{
    public string Name { get; init; } = string.Empty;
    public string? Description { get; init; }
    public string Address { get; init; } = string.Empty;
    public string City { get; init; } = string.Empty;
    public string? PostalCode { get; init; }
    public string? PhoneNumber { get; init; }
    public string? Email { get; init; }
    public string? Website { get; init; }
    public FacilityType Type { get; init; }
}

public class CreateFacilityCommandHandler : IRequestHandler<CreateFacilityCommand, int>
{
    private readonly ApplicationDbContext _context;

    public CreateFacilityCommandHandler(ApplicationDbContext context)
    {
        _context = context;
    }

    public async Task<int> Handle(CreateFacilityCommand request, CancellationToken cancellationToken)
    {
        var facility = new Facility
        {
            Name = request.Name,
            Description = request.Description,
            Address = request.Address,
            City = request.City,
            PostalCode = request.PostalCode,
            PhoneNumber = request.PhoneNumber,
            Email = request.Email,
            Website = request.Website,
            Type = request.Type,
            CreatedAt = DateTime.UtcNow
        };

        _context.Facilities.Add(facility);
        await _context.SaveChangesAsync(cancellationToken);

        return facility.Id;
    }
}
```

**Application/Queries/Facilities/GetFacilitiesQuery.cs:**
```csharp
using MediatR;
using Microsoft.EntityFrameworkCore;

namespace OazaDlaAutyzmu.Application.Queries.Facilities;

public record GetFacilitiesQuery : IRequest<List<FacilityDto>>
{
    public string? City { get; init; }
    public FacilityType? Type { get; init; }
    public VerificationStatus? Status { get; init; }
}

public class GetFacilitiesQueryHandler : IRequestHandler<GetFacilitiesQuery, List<FacilityDto>>
{
    private readonly ApplicationDbContext _context;

    public GetFacilitiesQueryHandler(ApplicationDbContext context)
    {
        _context = context;
    }

    public async Task<List<FacilityDto>> Handle(GetFacilitiesQuery request, CancellationToken cancellationToken)
    {
        var query = _context.Facilities
            .Include(f => f.VerifiedBy)
            .AsQueryable();

        if (!string.IsNullOrEmpty(request.City))
            query = query.Where(f => f.City.Contains(request.City));

        if (request.Type.HasValue)
            query = query.Where(f => f.Type == request.Type);

        if (request.Status.HasValue)
            query = query.Where(f => f.VerificationStatus == request.Status);

        return await query
            .Select(f => new FacilityDto
            {
                Id = f.Id,
                Name = f.Name,
                City = f.City,
                Type = f.Type.ToString(),
                VerificationStatus = f.VerificationStatus.ToString(),
                VerifiedByName = f.VerifiedBy != null ? f.VerifiedBy.UserName : null
            })
            .ToListAsync(cancellationToken);
    }
}
```

---

### FAZA 4: Controllers + Views (4-5 dni)

#### Krok 6: Controllers

**Web/Controllers/FacilitiesController.cs:**
```csharp
using MediatR;
using Microsoft.AspNetCore.Mvc;
using OazaDlaAutyzmu.Application.Commands.Facilities;
using OazaDlaAutyzmu.Application.Queries.Facilities;

namespace OazaDlaAutyzmu.Web.Controllers;

public class FacilitiesController : Controller
{
    private readonly IMediator _mediator;

    public FacilitiesController(IMediator mediator)
    {
        _mediator = mediator;
    }

    [HttpGet]
    public async Task<IActionResult> Index(string? city, FacilityType? type)
    {
        var query = new GetFacilitiesQuery { City = city, Type = type };
        var facilities = await _mediator.Send(query);
        return View(facilities);
    }

    [HttpGet]
    public async Task<IActionResult> Details(int id)
    {
        var query = new GetFacilityByIdQuery { Id = id };
        var facility = await _mediator.Send(query);
        
        if (facility == null)
            return NotFound();
            
        return View(facility);
    }

    [HttpGet]
    [Authorize(Roles = "Admin,Moderator")]
    public IActionResult Create()
    {
        return View();
    }

    [HttpPost]
    [Authorize(Roles = "Admin,Moderator")]
    [ValidateAntiForgeryToken]
    public async Task<IActionResult> Create(CreateFacilityCommand command)
    {
        if (!ModelState.IsValid)
            return View(command);

        var id = await _mediator.Send(command);
        return RedirectToAction(nameof(Details), new { id });
    }
}
```

#### Krok 7: Razor Views (z Tailwind CSS)

**Web/Views/Facilities/Index.cshtml:**
```html
@model List<FacilityDto>
@{
    ViewData["Title"] = "Placówki";
}

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Placówki dla osób z autyzmem
        </h1>
        @if (User.IsInRole("Admin") || User.IsInRole("Moderator"))
        {
            <a asp-action="Create" class="btn btn-primary">
                Dodaj placówkę
            </a>
        }
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach (var facility in Model)
        {
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        @facility.Name
                    </h3>
                    <span class="@GetVerificationBadgeClass(facility.VerificationStatus)">
                        @facility.VerificationStatus
                    </span>
                </div>
                
                <p class="text-gray-600 dark:text-gray-400 mb-2">
                    📍 @facility.City
                </p>
                
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    @facility.Type
                </p>
                
                <a asp-action="Details" asp-route-id="@facility.Id" 
                   class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                    Zobacz szczegóły →
                </a>
            </div>
        }
    </div>
</div>

@functions {
    string GetVerificationBadgeClass(string status) => status switch
    {
        "Certified" => "px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm",
        "Verified" => "px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm",
        "Flagged" => "px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm",
        _ => "px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm"
    };
}
```

---

### FAZA 5: Authentication + Authorization (2-3 dni)

#### Krok 8: Identity Configuration

**Web/Program.cs:**
```csharp
using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;
using OazaDlaAutyzmu.Domain.Entities;
using OazaDlaAutyzmu.Infrastructure.Data;

var builder = WebApplication.CreateBuilder(args);

// Add services
builder.Services.AddDbContext<ApplicationDbContext>(options =>
    options.UseNpgsql(builder.Configuration.GetConnectionString("DefaultConnection")));

builder.Services.AddIdentity<ApplicationUser, IdentityRole<int>>(options =>
{
    options.Password.RequireDigit = true;
    options.Password.RequireLowercase = true;
    options.Password.RequireUppercase = true;
    options.Password.RequiredLength = 8;
    options.SignIn.RequireConfirmedEmail = true;
})
.AddEntityFrameworkStores<ApplicationDbContext>()
.AddDefaultTokenProviders();

builder.Services.AddMediatR(cfg => 
    cfg.RegisterServicesFromAssembly(typeof(CreateFacilityCommand).Assembly));

builder.Services.AddControllersWithViews();
builder.Services.AddRazorPages();

var app = builder.Build();

// Middleware pipeline
if (app.Environment.IsDevelopment())
{
    app.UseDeveloperExceptionPage();
}
else
{
    app.UseExceptionHandler("/Error");
    app.UseHsts();
}

app.UseHttpsRedirection();
app.UseStaticFiles();
app.UseRouting();

app.UseAuthentication();
app.UseAuthorization();

app.MapControllerRoute(
    name: "default",
    pattern: "{controller=Home}/{action=Index}/{id?}");
app.MapRazorPages();

app.Run();
```

---

### FAZA 6: Testing (2-3 dni)

#### Krok 9: Unit Tests

**Tests/Unit/Commands/CreateFacilityCommandHandlerTests.cs:**
```csharp
using Microsoft.EntityFrameworkCore;
using OazaDlaAutyzmu.Application.Commands.Facilities;
using OazaDlaAutyzmu.Infrastructure.Data;
using Xunit;

namespace OazaDlaAutyzmu.Tests.Unit.Commands;

public class CreateFacilityCommandHandlerTests
{
    private ApplicationDbContext GetInMemoryDbContext()
    {
        var options = new DbContextOptionsBuilder<ApplicationDbContext>()
            .UseInMemoryDatabase(databaseName: Guid.NewGuid().ToString())
            .Options;

        return new ApplicationDbContext(options);
    }

    [Fact]
    public async Task Handle_ShouldCreateFacility_WhenCommandIsValid()
    {
        // Arrange
        var context = GetInMemoryDbContext();
        var handler = new CreateFacilityCommandHandler(context);
        var command = new CreateFacilityCommand
        {
            Name = "Test Facility",
            Address = "Test Address",
            City = "Warsaw",
            Type = FacilityType.Therapy
        };

        // Act
        var result = await handler.Handle(command, CancellationToken.None);

        // Assert
        Assert.True(result > 0);
        var facility = await context.Facilities.FindAsync(result);
        Assert.NotNull(facility);
        Assert.Equal("Test Facility", facility.Name);
    }
}
```

---

### FAZA 7: Deployment (2 dni)

#### Krok 10: Docker + Azure Deployment

**Dockerfile:**
```dockerfile
FROM mcr.microsoft.com/dotnet/aspnet:8.0 AS base
WORKDIR /app
EXPOSE 80
EXPOSE 443

FROM mcr.microsoft.com/dotnet/sdk:8.0 AS build
WORKDIR /src
COPY ["src/OazaDlaAutyzmu.Web/OazaDlaAutyzmu.Web.csproj", "OazaDlaAutyzmu.Web/"]
COPY ["src/OazaDlaAutyzmu.Application/OazaDlaAutyzmu.Application.csproj", "OazaDlaAutyzmu.Application/"]
COPY ["src/OazaDlaAutyzmu.Domain/OazaDlaAutyzmu.Domain.csproj", "OazaDlaAutyzmu.Domain/"]
COPY ["src/OazaDlaAutyzmu.Infrastructure/OazaDlaAutyzmu.Infrastructure.csproj", "OazaDlaAutyzmu.Infrastructure/"]
RUN dotnet restore "OazaDlaAutyzmu.Web/OazaDlaAutyzmu.Web.csproj"

COPY src/ .
WORKDIR "/src/OazaDlaAutyzmu.Web"
RUN dotnet build "OazaDlaAutyzmu.Web.csproj" -c Release -o /app/build

FROM build AS publish
RUN dotnet publish "OazaDlaAutyzmu.Web.csproj" -c Release -o /app/publish

FROM base AS final
WORKDIR /app
COPY --from=publish /app/publish .
ENTRYPOINT ["dotnet", "OazaDlaAutyzmu.Web.dll"]
```

**Azure deployment:**
```bash
az login
az group create --name OazaDlaAutyzmu --location northeurope
az appservice plan create --name OazaDlaAutyzmuPlan --resource-group OazaDlaAutyzmu --sku B1 --is-linux
az webapp create --name oaza-dla-autyzmu --resource-group OazaDlaAutyzmu --plan OazaDlaAutyzmuPlan --runtime "DOTNET:8.0"
az webapp deployment source config --name oaza-dla-autyzmu --resource-group OazaDlaAutyzmu --repo-url https://github.com/mart-gant/oaza-dla-autyzmu-dotnet --branch main
```

---

## Stack technologiczny

### Backend:
- **ASP.NET Core 8.0** - Web framework
- **Entity Framework Core 8.0** - ORM
- **PostgreSQL** - Database
- **MediatR** - CQRS pattern
- **FluentValidation** - Validation
- **AutoMapper** - Object mapping
- **Serilog** - Logging
- **Hangfire** - Background jobs

### Frontend:
- **Razor Pages** - Server-side rendering
- **Tailwind CSS** - Styling
- **Alpine.js** - JavaScript interactions
- **HTMX** - Dynamic content (opcjonalnie)

### Testing:
- **xUnit** - Unit testing
- **Moq** - Mocking
- **FluentAssertions** - Assertions
- **TestContainers** - Integration testing

### DevOps:
- **GitHub Actions** - CI/CD
- **Docker** - Containerization
- **Azure App Service** - Hosting
- **Azure PostgreSQL** - Database hosting

---

## Roadmapa rozwoju

### Etap 1: MVP (2-3 miesiące)
- ✅ Migracja core features z Laravel
- ✅ Authentication & Authorization
- ✅ Facilities CRUD + verification
- ✅ Forum basic functionality
- ✅ Articles management
- ✅ Deployment na Azure

### Etap 2: Premium Features (3-4 miesiące)
- 🔄 **Multi-tenancy** - każda placówka = osobna instancja
- 🔄 **Payment integration** (Stripe/Klarna) - subskrypcje
- 🔄 **Advanced analytics** - dashboardy dla placówek
- 🔄 **Mobile API** - REST API dla aplikacji mobilnej
- 🔄 **Real-time notifications** - SignalR

### Etap 3: Internationalization (2 miesiące)
- 🌍 **Multi-language** - NO, SV, DA, FI, EN
- 🌍 **Localized content** - artykuły w lokalnych językach
- 🌍 **Currency support** - NOK, SEK, DKK, EUR

### Etap 4: Enterprise Features (3-4 miesiące)
- 🏢 **SSO Integration** - Azure AD, SAML
- 🏢 **Advanced permissions** - role-based + claim-based
- 🏢 **Audit logging** - compliance (GDPR)
- 🏢 **White-label** - customizable branding
- 🏢 **REST API + GraphQL** - dla integracji

### Etap 5: AI & ML (2-3 miesiące)
- 🤖 **Chatbot** - Azure Bot Service
- 🤖 **Recommendation engine** - placówki dla użytkowników
- 🤖 **Content moderation** - Azure Content Moderator
- 🤖 **Translation** - Azure Translator

---

## Potencjalne źródła dochodu

### Model biznesowy:

1. **Freemium dla placówek:**
   - Free: podstawowy profil, max 5 zdjęć
   - Premium (€29/miesiąc): unlimited zdjęcia, analytics, featured listing
   - Enterprise (€99/miesiąc): multi-location, API access, priority support

2. **Licencje dla krajów:**
   - Sprzedaż white-label dla Norwegii, Szwecji, Danii, Finlandii
   - €5000-10000 setup fee + €500/miesiąc maintenance

3. **B2B dla szkół:**
   - Platform dla nauczycieli - €15/użytkownik/miesiąc
   - Moduł zarządzania uczniami z autyzmem

4. **Consulting:**
   - €100-150/godzinę - tworzenie podobnych platform
   - €5000-15000 za gotowy projekt

5. **Reklamy (delikatnie):**
   - Sponsorowane artykuły od terapeutów
   - Bannery dla certified facilities

### Szacunkowy dochód (rok 2):
- 50 placówek Premium: €1450/miesiąc
- 5 placówek Enterprise: €495/miesiąc
- 1 licencja white-label: €500/miesiąc
- Consulting: €2000/miesiąc (8h)
- **RAZEM: ~€4500/miesiąc (€54000/rok)**

---

## Następne kroki

### Natychmiast:
1. Stwórz nowe repozytorium: `oaza-dla-autyzmu-dotnet`
2. Setup podstawowej struktury projektu
3. Migruj najpierw model `Facility` jako proof of concept

### W tym tygodniu:
1. Migruj wszystkie domain models
2. Uruchom podstawowy CRUD dla facilities
3. Deploy na Azure (free tier)

### W tym miesiącu:
1. Migruj authentication
2. Migruj forum + articles
3. Uruchom beta dla wybranych użytkowników

---

## Pytania?

Gotowy zacząć? Powiedz mi:
1. **Co migrujemy jako pierwsze?** (facilities, forum, articles?)
2. **Masz Azure account?** (potrzebny do deployment)
3. **Preferujesz Razor Pages czy Blazor?** (dla frontend)

Mogę Ci pomóc krok po kroku! 🚀
