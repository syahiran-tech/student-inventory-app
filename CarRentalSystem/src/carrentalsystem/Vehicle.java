package carrentalsystem;

public abstract class Vehicle {
    private String vehicleId;
    private String brand;
    private String model;
    private int year;
    private String plateNumber;
    private String transmissionType;
    private int seatingCapacity;
    private double mileage;
    private String status;
    private double dailyRate;

    protected Vehicle() {
        this.vehicleId = ""; this.brand = ""; this.model = ""; this.year = 0;
        this.plateNumber = ""; this.transmissionType = "Auto"; this.seatingCapacity = 5;
        this.mileage = 0.0; this.status = "Available"; this.dailyRate = 0.0;
    }

    protected Vehicle(String vehicleId, String brand, String model, int year, String plateNumber,
                      String transmissionType, int seatingCapacity, double mileage, String status, double dailyRate) {
        this.vehicleId = vehicleId; this.brand = brand; this.model = model; this.year = year;
        this.plateNumber = plateNumber; this.transmissionType = transmissionType;
        this.seatingCapacity = seatingCapacity; this.mileage = mileage;
        this.status = status; this.dailyRate = dailyRate;
    }

    public String getVehicleId() { return vehicleId; }
    public void setVehicleId(String vehicleId) { this.vehicleId = vehicleId; }
    public String getBrand() { return brand; }
    public void setBrand(String brand) { this.brand = brand; }
    public String getModel() { return model; }
    public void setModel(String model) { this.model = model; }
    public int getYear() { return year; }
    public void setYear(int year) { this.year = year; }
    public String getPlateNumber() { return plateNumber; }
    public void setPlateNumber(String plateNumber) { this.plateNumber = plateNumber; }
    public String getTransmissionType() { return transmissionType; }
    public void setTransmissionType(String transmissionType) { this.transmissionType = transmissionType; }
    public int getSeatingCapacity() { return seatingCapacity; }
    public void setSeatingCapacity(int seatingCapacity) { this.seatingCapacity = seatingCapacity; }
    public double getMileage() { return mileage; }
    public void setMileage(double mileage) { this.mileage = mileage; }
    public String getStatus() { return status; }
    public void setStatus(String status) { this.status = status; }
    public double getDailyRate() { return dailyRate; }
    public void setDailyRate(double dailyRate) { this.dailyRate = dailyRate; }

    public abstract String getCategory();
    public abstract double calculateRentalCost(int days);

    @Override
    public String toString() {
        return String.format("%-6s %-10s %-10s %d  %-8s %-6s %d seats  %.0fkm  %-11s RM%.2f/day [%s]",
                vehicleId, brand, model, year, plateNumber, transmissionType,
                seatingCapacity, mileage, status, dailyRate, getCategory());
    }

    @Override
    public boolean equals(Object obj) {
        if (this == obj) return true;
        if (obj == null || !(obj instanceof Vehicle)) return false;
        return this.vehicleId.equals(((Vehicle) obj).vehicleId);
    }
}
