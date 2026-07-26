package carrentalsystem;

public class Supplier {
    private String supplierId, companyName, contactPerson, phone, email, serviceType;

    public Supplier() {}

    public Supplier(String supplierId, String companyName, String contactPerson, String phone, String email, String serviceType) {
        this.supplierId = supplierId; this.companyName = companyName; this.contactPerson = contactPerson;
        this.phone = phone; this.email = email; this.serviceType = serviceType;
    }

    public String getSupplierId() { return supplierId; } public void setSupplierId(String v) { supplierId = v; }
    public String getCompanyName() { return companyName; } public void setCompanyName(String v) { companyName = v; }
    public String getContactPerson() { return contactPerson; } public void setContactPerson(String v) { contactPerson = v; }
    public String getPhone() { return phone; } public void setPhone(String v) { phone = v; }
    public String getEmail() { return email; } public void setEmail(String v) { email = v; }
    public String getServiceType() { return serviceType; } public void setServiceType(String v) { serviceType = v; }

    @Override
    public String toString() {
        return String.format("%-6s %-20s %-15s %-12s %-20s %s", supplierId, companyName, contactPerson, phone, email, serviceType);
    }

    @Override public boolean equals(Object obj) {
        if (this == obj) return true;
        if (obj == null || !(obj instanceof Supplier)) return false;
        return this.supplierId.equals(((Supplier) obj).supplierId);
    }
}
