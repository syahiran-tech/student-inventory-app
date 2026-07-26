package carrentalsystem;

public class Booking {
    private String bookingId, customerId, vehicleId, customerName, vehicleName;
    private String startDate, endDate, bookingDate, status;
    private int rentalDays;
    private double totalCost;

    public Booking() { this.bookingId = ""; this.status = "Active"; }

    public Booking(String bookingId, String customerId, String vehicleId, String customerName,
                   String vehicleName, String startDate, String endDate, int rentalDays,
                   double totalCost, String status, String bookingDate) {
        this.bookingId = bookingId; this.customerId = customerId; this.vehicleId = vehicleId;
        this.customerName = customerName; this.vehicleName = vehicleName;
        this.startDate = startDate; this.endDate = endDate; this.rentalDays = rentalDays;
        this.totalCost = totalCost; this.status = status; this.bookingDate = bookingDate;
    }

    public String getBookingId() { return bookingId; } public void setBookingId(String v) { bookingId = v; }
    public String getCustomerId() { return customerId; } public void setCustomerId(String v) { customerId = v; }
    public String getVehicleId() { return vehicleId; } public void setVehicleId(String v) { vehicleId = v; }
    public String getCustomerName() { return customerName; } public void setCustomerName(String v) { customerName = v; }
    public String getVehicleName() { return vehicleName; } public void setVehicleName(String v) { vehicleName = v; }
    public String getStartDate() { return startDate; } public void setStartDate(String v) { startDate = v; }
    public String getEndDate() { return endDate; } public void setEndDate(String v) { endDate = v; }
    public String getBookingDate() { return bookingDate; } public void setBookingDate(String v) { bookingDate = v; }
    public String getStatus() { return status; } public void setStatus(String v) { status = v; }
    public int getRentalDays() { return rentalDays; } public void setRentalDays(int v) { rentalDays = v; }
    public double getTotalCost() { return totalCost; } public void setTotalCost(double v) { totalCost = v; }

    @Override
    public String toString() {
        return String.format("%-6s %-8s %-6s %-12s %-18s %s to %s  %dd  RM%.2f  [%s]",
                bookingId, customerId, vehicleId, customerName, vehicleName,
                startDate, endDate, rentalDays, totalCost, status);
    }

    @Override public boolean equals(Object obj) {
        if (this == obj) return true;
        if (obj == null || !(obj instanceof Booking)) return false;
        return this.bookingId.equals(((Booking) obj).bookingId);
    }
}
