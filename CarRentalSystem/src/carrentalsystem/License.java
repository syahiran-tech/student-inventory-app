package carrentalsystem;

public class License {
    private String licenseNumber;
    private String licenseType;
    private String expiryDate;

    public License() { this.licenseNumber = ""; this.licenseType = ""; this.expiryDate = ""; }

    public License(String licenseNumber, String licenseType, String expiryDate) {
        this.licenseNumber = licenseNumber; this.licenseType = licenseType; this.expiryDate = expiryDate;
    }

    public String getLicenseNumber() { return licenseNumber; }
    public void setLicenseNumber(String licenseNumber) { this.licenseNumber = licenseNumber; }
    public String getLicenseType() { return licenseType; }
    public void setLicenseType(String licenseType) { this.licenseType = licenseType; }
    public String getExpiryDate() { return expiryDate; }
    public void setExpiryDate(String expiryDate) { this.expiryDate = expiryDate; }

    @Override
    public String toString() {
        return String.format("License#: %s | Type: %s | Expiry: %s", licenseNumber, licenseType, expiryDate);
    }

    @Override
    public boolean equals(Object obj) {
        if (this == obj) return true;
        if (obj == null || !(obj instanceof License)) return false;
        return this.licenseNumber.equals(((License) obj).licenseNumber);
    }
}
